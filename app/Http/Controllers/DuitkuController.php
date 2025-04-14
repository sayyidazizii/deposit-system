<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DuitkuController extends Controller
{
    public function callback(Request $request)
    {
        try {
            Log::info('Duitku Callback Received', $request->all());

            $merchantOrderId = $request->merchantOrderId;
            $statusCode = $request->resultCode;
            $amount = $request->amount;
            $signature = $request->signature;

            $apiKey = config('duitku.api_key');
            $merchantCode = config('duitku.merchant_code');

            $expectedSignature = md5($merchantCode . $merchantOrderId . $amount . $apiKey);

            if ($signature !== $expectedSignature) {
                Log::warning("Invalid signature on Duitku callback", [
                    'expected' => $expectedSignature,
                    'received' => $signature,
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $deposit = Deposit::find($merchantOrderId);
            if (!$deposit) {
                Log::warning("Deposit not found. ID: {$merchantOrderId}");
                return response()->json(['message' => 'Deposit not found'], 404);
            }

            if ($deposit->status === 'paid') {
                Log::info("Deposit status paid. Skip. ID: {$deposit->id}");
                return response()->json(['message' => 'Already processed'], 200);
            }

            if ($statusCode === '00') {
                $deposit->status = 'paid';
                $deposit->save();

                $wallet = Wallet::firstOrCreate(['user_id' => $deposit->user_id]);
                $wallet->increment('balance', $deposit->amount + $deposit->cashback);

                Log::info("Deposit success. ID: {$deposit->id}, User: {$deposit->user_id}, Total: " . ($deposit->amount + $deposit->cashback));
            } else {
                $deposit->status = 'failed';
                $deposit->save();

                Log::info("Deposit failed. ID: {$deposit->id}, Status Code: {$statusCode}");
            }

            return response()->json(['message' => 'Callback processed successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error in Duitku callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }


}

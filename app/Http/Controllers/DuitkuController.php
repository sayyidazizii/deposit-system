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

            $depositId = $request->merchantOrderId;
            $status = $request->resultCode;

            $deposit = Deposit::find($depositId);
            if (!$deposit) {
                Log::warning("Deposit not found. ID: {$depositId}");
                return response()->json(['message' => 'Not found'], 404);
            }

            if ($status == '00') {
                $deposit->update(['status' => 'paid']);

                $wallet = Wallet::firstOrCreate(['user_id' => $deposit->user_id]);
                $wallet->increment('balance', $deposit->amount + $deposit->cashback);

                Log::info("Deposit success. ID: {$deposit->id}, User: {$deposit->user_id}, Amount: {$deposit->amount}");
            } else {
                $deposit->update(['status' => 'failed']);
                Log::info("Deposit gagal. ID: {$deposit->id}, Status Code: {$status}");
            }

            return response()->json(['message' => 'Callback received']);
        } catch (\Exception $e) {
            Log::error('Error handling Duitku callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }


}

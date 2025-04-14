<?php
namespace App\Http\Controllers;


use App\Models\Wallet;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use AdityaDarma\LaravelDuitku\Facades\DuitkuAPI;

class DepositController extends Controller
{

    public function index()
    {
        $deposits = Deposit::orderBy('created_at', 'desc')->get();
        return view('deposit.index', compact('deposits'));
    }

    public function history()
    {
        $deposits = Deposit::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('deposit.history', compact('deposits'));
    }

    public function create()
    {
        $paymentMethods = $this->getPaymentMethodsArray();
        return view('deposit.add', compact('paymentMethods'));
    }

    public function getPaymentMethodsArray()
    {

        $merchantCode = config('duitku.merchant_code');
        $apiKey = config('duitku.api_key');
        $amount = 10000; // Min amount
        $datetime = now()->format('Y-m-d H:i:s');

        $signature = hash('sha256', $merchantCode . $amount . $datetime . $apiKey);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod', [
            'merchantcode' => $merchantCode,
            'amount' => $amount,
            'datetime' => $datetime,
            'signature' => $signature
        ]);

        $data = $response->json();
        // dd($data);
        return $data['paymentFee'] ?? [];
    }


    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        if ($request->amount < 10000) {
            return back()->with('error', 'Jumlah deposit minimal adalah 10.000');
        }
        elseif ($request->payment_reference == null) {
            return back()->with('error', 'Silakan pilih metode pembayaran.');
        }

        DB::beginTransaction();
        try {
            $amount = $request->amount;
            $payment_reference = $request->payment_reference;
            $user = Auth::user();

            // *cashback
            $cashback = 0;
            if ($amount >= 15000000) {
                $cashback = $amount * 0.20;
            } elseif ($amount >= 10000000) {
                $cashback = $amount * 0.12;
            } elseif ($amount >= 5000000) {
                $cashback = $amount * 0.05;
            }

            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'cashback' => $cashback,
                'payment_reference' => $payment_reference,
                'status' => 'pending',
            ]);

            $merchantCode = config('duitku.merchant_code');
            $apiKey = config('duitku.api_key');
            $merchantOrderId = $deposit->id;
            $productDetails = 'Topup Saldo';
            $signature = md5($merchantCode . $merchantOrderId . $amount . $apiKey);

            $params = [
                'merchantCode' => $merchantCode,
                'paymentAmount' => $amount,
                'paymentMethod' => $payment_reference,
                'merchantOrderId' => $merchantOrderId,
                'productDetails' => $productDetails,
                'customerVaName' => $user->name,
                'email' => $user->email,
                'phoneNumber' => $user->phone ?? '',
                'callbackUrl' => config('duitku.callback_url'),
                'returnUrl' => config('duitku.return_url'),
                'signature' => $signature,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $params);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['paymentUrl'])) {
                    $depositData = Deposit::find($merchantOrderId);
                    $depositData->code_reference = $result['reference'];
                    $depositData->vaNumber = $result['vaNumber'];
                    $depositData->payment_url = $result['paymentUrl'];
                    $depositData->signature = $signature;
                    $depositData->save();
                    log::info('Duitku Payment URL', [
                        'merchantOrderId' => $merchantOrderId,
                        'resutl' => $result,
                    ]);
                    // return $result['paymentUrl'];
                    DB::commit();
                    return redirect($result['paymentUrl']);
                } else {
                    return redirect('dashboard')->with('error', 'Gagal mendapatkan URL pembayaran.');
                }
            } else {
                DB::rollBack();
                Log::error('Duitku inquiry gagal', [
                    'response' => $response->json(),
                    'request' => $params,
                ]);
                return back()->with('error', 'Terjadi kesalahan saat memproses permintaan pembayaran, coba gunakan payment method lain.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat transaksi Duitku', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses deposit. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $deposit = Deposit::findOrFail($id);
        return view('deposit.detail', compact('deposit'));
    }

    public function return(Request $request)
    {
        $merchantOrderId = $request->query('merchantOrderId');
        $reference = $request->query('reference');
        $resultCode = $request->query('resultCode');

        if ($merchantOrderId && $reference && $resultCode) {
            $deposit = Deposit::where('id', $merchantOrderId)->first();

            if ($deposit) {
                if ($resultCode == '00') {
                    $deposit->status = 'paid';
                    $message = 'Pembayaran berhasil!';
                    $status = 'success';
                } elseif ($resultCode == '01') {
                    $deposit->status = 'pending';
                    $message = 'Pembayaran Anda masih pending, harap menunggu.';
                    $status = 'pending';
                } elseif ($resultCode == '02') {
                    $deposit->status = 'failed';
                    $message = 'Pembayaran Anda gagal.';
                    $status = 'failed';
                }

                $deposit->save();

                $transactionDetails = [
                    'merchantOrderId' => $deposit->id,
                    'amount' => $deposit->amount,
                    'cashback' => $deposit->cashback,
                    'vaNumber' => $deposit->vaNumber,
                    'payment_url' => $deposit->payment_url,
                    'paymentReference' => $reference,
                    'transactionTime' => $deposit->created_at->format('Y-m-d H:i:s'),
                    'paymentStatus' => $status,
                ];
            } else {
                $message = 'Transaksi tidak ditemukan.';
                $status = 'error';
                $transactionDetails = [];
            }
        } else {
            $message = 'Parameter tidak lengkap.';
            $status = 'error';
            $transactionDetails = [];
        }

        return view('deposit.status', compact('message', 'status', 'transactionDetails'));
    }

    public function manualCallback($id)
    {
        DB::beginTransaction();
        try {
            $deposit = Deposit::find($id);

            $merchantOrderId = $deposit->id;
            $statusCode = '00'; // Assuming paid code dari Duitku
            $amount = $deposit->amount;
            $signature = $deposit->signature;

            if (!$deposit) {
                Log::warning("Deposit not found. ID: {$merchantOrderId}");
                return redirect('deposit')->with('error', 'Deposit not found');
            }

            if ($deposit->status === 'paid') {
                Log::info("Deposit status paid. Skip. ID: {$deposit->id}");
                return redirect('deposit')->with('error', 'Deposit sudah diproses');
            }

            if ($statusCode === '00') {
                $deposit->status = 'paid';
                $deposit->save();

                $wallet = Wallet::firstOrCreate(['user_id' => $deposit->user_id]);
                $wallet->increment('balance', $deposit->amount + $deposit->cashback);

                Log::info("Manual Deposit success. ID: {$deposit->id}, User: {$deposit->user_id}, Total: " . ($deposit->amount + $deposit->cashback));
            } else {
                $deposit->status = 'failed';
                $deposit->save();

                Log::info("Manual Deposit failed. ID: {$deposit->id}, Status Code: {$statusCode}");
                return redirect('deposit')->with('error', 'Manual Callback processed failed');
            }
            DB::commit();
            return redirect('deposit')->with('success', 'Manual Callback processed successfully');
            // return response()->json(['message' => 'Manual Callback processed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Duitku callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return redirect('deposit')->with('error', 'Manual Callback processed failed');
            // return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

}

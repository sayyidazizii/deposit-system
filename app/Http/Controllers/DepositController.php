<?php
namespace App\Http\Controllers;


use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use AdityaDarma\LaravelDuitku\Facades\DuitkuAPI;

class DepositController extends Controller
{

    public function create()
    {
        $paymentMethods = $this->getPaymentMethodsArray();
        return view('deposit.add', compact('paymentMethods'));
    }

    public function getPaymentMethodsArray()
    {

        $merchantCode = config('duitku.merchant_code');
        $apiKey = config('duitku.api_key');
        $amount = 10000;
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
        return $data['paymentFee'] ?? [];
    }


    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

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
                    return redirect($result['paymentUrl']);
                } else {
                    return redirect('dashboard')->with('error', 'Gagal mendapatkan URL pembayaran.');
                }
            } else {
                return redirect('redirect')->with('error', 'Terjadi kesalahan saat memproses permintaan pembayaran.');
            }

        } catch (\Exception $e) {
            Log::error('Gagal membuat transaksi Duitku', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses deposit. Silakan coba lagi.');
        }
    }

    public function redirect(Request $request)
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
                    'merchantOrderId' => $deposit->payment_reference,
                    'amount' => $deposit->amount,
                    'cashback' => $deposit->cashback,
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


}

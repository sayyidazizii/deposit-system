<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AdityaDarma\LaravelDuitku\Facades\DuitkuAPI;

class TransactionController extends Controller
{
    public function checkTransactionStatus($merchantOrderId)
    {
        try {
            // Memeriksa status transaksi dengan merchantOrderId
            $response = DuitkuAPI::checkTransactionStatus($merchantOrderId);

            // Mengecek apakah status transaksi berhasil atau gagal
            if ($response['responseCode'] === '00') {
                // Transaksi berhasil
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction successfully processed.',
                    'transaction_details' => $response
                ]);
            } else {
                // Transaksi gagal
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaction failed.',
                    'transaction_details' => $response
                ]);
            }
        } catch (\Exception $e) {
            // Menangani error jika ada masalah dengan API call
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking transaction status: ' . $e->getMessage()
            ], 500);
        }
    }
}

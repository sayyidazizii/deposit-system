<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Deposit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('name', 'user')->first();

        // Deposit::create([
        //     'user_id' => $user->id,
        //     'amount' => 10000000,
        //     'cashback' => 1200000,
        //     'status' => 'paid',
        //     'code_reference' => 'DUMMY12345',
        //     'vaNumber' => '1234567890',
        //     'qrString' => 'https://dummyurl.com/qr/12345',
        //     'payment_reference' => 'DUMMY12345',
        //     'payment_url' => 'https://dummyurl.com/payment/12345',
        //     'signature' => '1234567890abcdef1234567890abcdef',
        // ]);
    }
}

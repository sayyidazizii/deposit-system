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

        Deposit::create([
            'user_id' => $user->id,
            'amount' => 10000000,
            'cashback' => 1200000,
            'status' => 'paid',
            'payment_reference' => 'DUMMY12345',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('name', 'user')->first();
        $product = Product::first();

        Transaction::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'amount' => $product->price,
        ]);
    }
}

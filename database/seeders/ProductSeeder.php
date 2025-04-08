<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            ['name' => 'Produk A', 'price' => 100000,'stock' => 10],
            ['name' => 'Produk B', 'price' => 250000,'stock' => 5],
            ['name' => 'Produk C', 'price' => 750000,'stock' => 2],
        ]);
    }
}

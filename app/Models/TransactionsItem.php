<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionsItem extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionsItemFactory> */
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

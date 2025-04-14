<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\TransactionsItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'user_id',
        'amount',
        'transaction_number',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function items()
    {
        return $this->hasMany(TransactionsItem::class);
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
    public function scopeFilterByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }
}

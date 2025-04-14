<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Models\TransactionsItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function index()
    {
        $transactions = Transaction::latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    public function list()
    {
        $transactions = Transaction::where('user_id', Auth::id())->latest()->get();
        return view('transactions.list', compact('transactions'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }

        session(['cart' => $cart]);
        return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang kosong.');
        }

        DB::beginTransaction();
        // cek jika saldo tidak cukup
        $user = Auth::user();

        if (!$user->wallet) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Wallet tidak ditemukan.');
        }

        if ($user->wallet->balance < collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        })) {
            DB::rollBack();
            Log::info('User Balance: ' . $user->balance);
            Log::info('Total Amount: ' . collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            }));
            Log::info('cart'.json_encode($cart));
            return redirect()->back()->with('error', 'Saldo tidak cukup.');
        }

        try {
            $totalAmount = collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'transaction_number' => strtoupper(Str::random(10)),
                'amount' => $totalAmount,
                'status' => 'success',
            ]);

            $user->wallet->balance -= $totalAmount;
            $user->wallet->save();

            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);
                if ($product->stock < $item['quantity']) {
                    return redirect()->back()->with('error', 'Stok tidak cukup untuk produk: ' . $item['name']);
                }
                $product->stock -= $item['quantity'];
                $product->save();
                TransactionsItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);

            }

            session()->forget('cart');

            DB::commit();
            return redirect()->route('transactions.user')->with('success', 'Checkout berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat checkout: ' . $e->getMessage());
        }
    }


}

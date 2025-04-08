<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index',compact('products'));
    }

    public function list()
    {
        $products = Product::all();
        return view('list_products',compact('products'));
    }

    public function all()
    {
        $products = Product::all();
        return view('products.list_products',compact('products'));
    }

    public function create()
    {
        return view('products.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try{
            $product = Product::create($request->all());
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Product creation failed: ' . $e->getMessage());
            return redirect()->route('product.index')->with('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->route('product.index')->with('success', 'Tambah Produk Berhasil.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try{
            $product    = Product::findOrFail($request->id);
            $product->update($request->all());
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Product update failed: ' . $e->getMessage());
            return redirect()->route('product.index')->with('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->route('product.index')->with('success', 'Update Produk Berhasil.');
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try{
            $product->delete();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Product deletion failed: ' . $e->getMessage());
            return redirect()->route('product.index')->with('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->route('product.index')->with('success', 'Hapus Produk Berhasil.');
    }
}

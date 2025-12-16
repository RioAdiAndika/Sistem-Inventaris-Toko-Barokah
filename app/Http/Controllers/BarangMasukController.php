<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function index()
    {
        $data = BarangMasuk::with('product')->latest()->get();
        return view('barang_masuk.index', compact('data'));
    }

    public function create()
    {
        $products = Product::all();
        return view('barang_masuk.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date'
        ]);

        BarangMasuk::create($request->all());

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil dicatat');
    }
}


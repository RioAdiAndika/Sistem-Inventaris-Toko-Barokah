<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $data = BarangKeluar::with('product')
            ->latest()
            ->get()
            ->map(function ($item) {
                // Format tanggal keluar
                $item->tanggal_format = Carbon::parse($item->tanggal)->format('d-m-Y');

                // Format tanggal kadaluarsa
                $item->tanggal_kadaluarsa_format = $item->tanggal_kadaluarsa
                    ? Carbon::parse($item->tanggal_kadaluarsa)->format('d-m-Y')
                    : '-';

                return $item;
            });

        return view('barang_keluar.index', compact('data'));
    }

    public function create()
    {
        // Ambil hanya barang yang stoknya tersedia
        $products = Product::orderBy('nama_barang')->get(); // hapus filter stok > 0

        return view('barang_keluar.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'         => 'required|exists:products,id',
            'jumlah'             => 'required|integer|min:1',
            'tanggal'            => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);

            // Validasi stok
            if ($request->jumlah > $product->stok) {
                throw new \Exception('Stok tidak mencukupi');
            }

            // Simpan barang keluar
            BarangKeluar::create([
                'product_id'         => $request->product_id,
                'jumlah'             => $request->jumlah,
                'tanggal'            => $request->tanggal,
            ]);

            // Kurangi stok produk
            $product->decrement('stok', $request->jumlah);
        });

        return redirect()
            ->route('barang-keluar.index')
            ->with('success', 'Barang keluar berhasil disimpan');
    }
}

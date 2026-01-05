<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BarangMasukController extends Controller
{
    public function index()
    {
        $data = BarangMasuk::with('product')
            ->latest()
            ->get()
            ->map(function ($item) {

                // Format tanggal masuk
                $item->tanggal_format = Carbon::parse($item->tanggal)->format('d-m-Y');

                // Format tanggal kadaluarsa
                $item->tanggal_kadaluarsa_format = $item->tanggal_kadaluarsa
                    ? Carbon::parse($item->tanggal_kadaluarsa)->format('d-m-Y')
                    : '-';

                // Default status
                $item->status_expired = 'Tanpa exp';
                $item->badge = 'secondary';
                $item->row_class = '';

                if ($item->tanggal_kadaluarsa) {
                    $expired = Carbon::parse($item->tanggal_kadaluarsa);
                    $now = Carbon::now();
                    $diffDays = $now->diffInDays($expired, false);

                    if ($diffDays < 0) {
                        $item->status_expired = 'Expired';
                        $item->badge = 'danger';
                        $item->row_class = 'table-danger';
                    } elseif ($diffDays <= 90) {
                        $item->status_expired = 'Hampir expired';
                        $item->badge = 'warning';
                        $item->row_class = 'table-warning';
                    } else {
                        $item->status_expired = 'Aman';
                        $item->badge = 'success';
                    }
                }

                return $item;
            });

        return view('barang_masuk.index', compact('data'));
    }

    public function create()
    {
        $products = Product::orderBy('nama_barang')->get();
        return view('barang_masuk.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'tanggal_kadaluarsa' => 'nullable|date|after_or_equal:tanggal',
        ]);

        BarangMasuk::create($validated);

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil dicatat');
    }
}

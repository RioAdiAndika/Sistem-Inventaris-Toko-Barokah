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
        $data = BarangMasuk::with(['product', 'satuan'])
            ->latest()
            ->get()
            ->map(function ($item) {

                // Format tanggal masuk
                $item->tanggal_format = \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y');

                // Format tanggal kadaluarsa
                $item->tanggal_kadaluarsa_format = $item->tanggal_kadaluarsa
                    ? \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d-m-Y')
                    : '-';

                // Default status
                $item->status_expired = 'Tanpa exp';
                $item->badge = 'secondary';
                $item->row_class = '';

                if ($item->tanggal_kadaluarsa) {
                    $expired = \Carbon\Carbon::parse($item->tanggal_kadaluarsa);
                    $now = \Carbon\Carbon::now();
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
        $products = Product::with('satuans')->orderBy('nama_barang')->get();
        return view('barang_masuk.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'satuan_id'  => 'required|exists:satuans,id',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'tanggal_kadaluarsa' => 'nullable|date',
        ]);


        // 🔴 CEK TANGGAL KADALUARSA
        if ($request->tanggal_kadaluarsa) {
            if (Carbon::parse($request->tanggal_kadaluarsa)->lt(Carbon::today())) {
                return back()
                    ->withInput()
                    ->with('error', 'Tanggal kadaluarsa sudah lewat dari hari ini');
            }
        }

        BarangMasuk::create([
            'product_id' => $request->product_id,
            'satuan_id'  => $request->satuan_id,
            'jumlah'     => $request->jumlah,
            'tanggal'    => $request->tanggal,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
        ]);


        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }
}

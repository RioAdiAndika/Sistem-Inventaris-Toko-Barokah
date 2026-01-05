<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangKeluarController extends Controller
{
    // =============================
    // INDEX
    // =============================
    public function index()
    {
        $data = BarangKeluar::with(['product', 'barangMasuk'])
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->tanggal_format = Carbon::parse($item->tanggal)->format('d-m-Y');
                return $item;
            });

        return view('barang_keluar.index', compact('data'));
    }

    // =============================
    // CREATE
    // =============================
    public function create()
    {
        $products = Product::all();

        $stock_per_exp = BarangMasuk::select(
                'product_id',
                'tanggal_kadaluarsa',
                DB::raw('SUM(jumlah) as total_masuk')
            )
            ->groupBy('product_id', 'tanggal_kadaluarsa')
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    $keluar = BarangKeluar::whereHas('barangMasuk', function ($q) use ($item) {
                        $q->where('product_id', $item->product_id)
                          ->whereDate('tanggal_kadaluarsa', $item->tanggal_kadaluarsa);
                    })->sum('jumlah');

                    return [
                        'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa,
                        'total_stok' => $item->total_masuk - $keluar
                    ];
                });
            });

        return view('barang_keluar.create', compact('products', 'stock_per_exp'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'tanggal_kadaluarsa' => 'nullable',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $barangMasuk = BarangMasuk::where('product_id', $request->product_id)
                    ->whereDate('tanggal_kadaluarsa', $request->tanggal_kadaluarsa)
                    ->firstOrFail();

                $totalKeluar = BarangKeluar::where('barang_masuk_id', $barangMasuk->id)
                    ->sum('jumlah');

                $sisa = $barangMasuk->jumlah - $totalKeluar;

                if ($request->jumlah > $sisa) {
                    throw new \Exception('Stok tidak mencukupi');
                }

                BarangKeluar::create([
                    'product_id' => $request->product_id,
                    'barang_masuk_id' => $barangMasuk->id,
                    'jumlah' => $request->jumlah,
                    'tanggal' => $request->tanggal,
                ]);
            });

            return redirect()
                ->route('barang-keluar.index')
                ->with('success', 'Barang berhasil dikeluarkan');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

}

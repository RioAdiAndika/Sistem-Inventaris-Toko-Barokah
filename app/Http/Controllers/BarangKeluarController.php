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
    $products = Product::orderBy('nama_barang')->get();

    $stock_per_exp = BarangMasuk::with('barangKeluar')
        ->get()
        ->groupBy('product_id')
        ->map(function ($items) {

            return $items
                ->groupBy(fn ($i) => $i->tanggal_kadaluarsa.'|'.$i->satuan)
                ->map(function ($group) {

                    $totalMasuk = $group->sum('jumlah');
                    $totalKeluar = $group->sum(fn ($i) => $i->barangKeluar->sum('jumlah'));
                    $sisa = $totalMasuk - $totalKeluar;

                    if ($sisa <= 0) return null;

                    return [
                        'barang_masuk_id'    => $group->first()->id,
                        'tanggal_kadaluarsa' => $group->first()->tanggal_kadaluarsa,
                        'satuan'             => $group->first()->satuan,
                        'total_stok'         => $sisa,
                    ];
                })
                ->filter()
                ->values();
        });

    return view('barang_keluar.create', compact('products', 'stock_per_exp'));
}




    // =============================
    // STORE
    // =============================
    public function store(Request $request)
{
    $request->validate([
        'product_id'        => 'required',
        'barang_masuk_id'   => 'required|exists:barang_masuk,id',
        'jumlah'            => 'required|integer|min:1',
        'satuan'            => 'required',
        'tanggal'           => 'required|date',
    ]);

    try {
        DB::transaction(function () use ($request) {

            $batch = BarangMasuk::with('barangKeluar')
                ->lockForUpdate()
                ->findOrFail($request->barang_masuk_id);

            $stokKeluar    = $batch->barangKeluar->sum('jumlah');
            $stokTersedia  = $batch->jumlah - $stokKeluar;

            if ($request->jumlah > $stokTersedia) {
                throw new \Exception(
                    'Stok tidak mencukupi. Sisa stok: '.$stokTersedia
                );
            }

            BarangKeluar::create([
                'product_id'      => $request->product_id,
                'barang_masuk_id' => $batch->id,
                'jumlah'          => $request->jumlah,
                'satuan'          => $request->satuan,
                'tanggal'         => $request->tanggal,
            ]);
        });

        return redirect()
            ->route('barang-keluar.index')
            ->with('success', 'Barang berhasil dikeluarkan');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}


}

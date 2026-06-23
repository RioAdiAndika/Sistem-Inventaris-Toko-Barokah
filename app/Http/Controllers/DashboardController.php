<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function admin()
    {
        $today = Carbon::today();
        $warningDays = 90;

        // =====================
        // STATISTIK UTAMA
        // =====================
        $totalBarang = Product::count();

        $barangMasukHariIni = BarangMasuk::whereDate('tanggal', $today)->sum('jumlah');
        $barangKeluarHariIni = BarangKeluar::whereDate('tanggal', $today)->sum('jumlah');

        $stokMenipis = Product::whereNotNull('stok_minimal_satuan_id')
    ->whereRaw("
        (
            SELECT COALESCE(SUM(bm.jumlah),0)
            FROM barang_masuk bm
            WHERE bm.product_id = products.id
            AND bm.satuan_id = products.stok_minimal_satuan_id
        )
        -
        (
            SELECT COALESCE(SUM(bk.jumlah),0)
            FROM barang_keluar bk
            WHERE bk.product_id = products.id
            AND bk.satuan_id = products.stok_minimal_satuan_id
        )
        <= products.stok_minimal
    ")
    ->count();


        // =====================
        // EXPIRED
        // =====================
        $barangExpired = BarangMasuk::whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', $today)
            ->count();

        $barangHampirExpired = BarangMasuk::whereNotNull('tanggal_kadaluarsa')
            ->whereBetween('tanggal_kadaluarsa', [
                $today->copy()->addDay(),
                $today->copy()->addDays($warningDays)
            ])->count();

        // =====================
        // AKTIVITAS HARI INI
        // =====================
        $aktivitasHariIni = collect([
            ...BarangMasuk::with('product', 'satuan')
                ->whereDate('tanggal', $today)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($x) {
                    $satuan = $x->satuan?->nama ?? '';
                    return [
                        'text' => "Barang masuk: {$x->product->nama_barang} {$x->jumlah} {$satuan}",
                        'tanggal' => $x->created_at
                    ];
                }),
            ...BarangKeluar::with('product', 'satuan')
                ->whereDate('tanggal', $today)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($x) {
                    $satuan = $x->satuan?->nama ?? '';
                    return [
                        'text' => "Barang keluar: {$x->product->nama_barang} {$x->jumlah} {$satuan}",
                        'tanggal' => $x->created_at
                    ];
                }),
        ])->sortByDesc('tanggal')->take(5);

        // =====================
        // STOK KRITIS DETAIL
        // =====================
        $stokKritis = Product::select(
        'products.*',
        'satuans.nama as stok_satuan'
    )
    ->selectRaw("
        (
            SELECT COALESCE(SUM(bm.jumlah),0)
            FROM barang_masuk bm
            WHERE bm.product_id = products.id
            AND bm.satuan_id = products.stok_minimal_satuan_id
        )
        -
        (
            SELECT COALESCE(SUM(bk.jumlah),0)
            FROM barang_keluar bk
            WHERE bk.product_id = products.id
            AND bk.satuan_id = products.stok_minimal_satuan_id
        ) AS stok_aktual
    ")
    ->leftJoin('satuans', 'satuans.id', '=', 'products.stok_minimal_satuan_id')
    ->whereNotNull('products.stok_minimal_satuan_id')
    ->whereRaw("
        (
            SELECT COALESCE(SUM(bm.jumlah),0)
            FROM barang_masuk bm
            WHERE bm.product_id = products.id
            AND bm.satuan_id = products.stok_minimal_satuan_id
        )
        -
        (
            SELECT COALESCE(SUM(bk.jumlah),0)
            FROM barang_keluar bk
            WHERE bk.product_id = products.id
            AND bk.satuan_id = products.stok_minimal_satuan_id
        )
        <= products.stok_minimal
    ")
    ->get();



        // =====================
        // DETAIL EXPIRED
        // =====================
        $expiredItems = BarangMasuk::with('product.satuans')
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', $today)
            ->orderBy('tanggal_kadaluarsa')
            ->take(5)
            ->get();

        $hampirExpiredItems = BarangMasuk::with('product.satuans')
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereBetween('tanggal_kadaluarsa', [
                $today->copy()->addDay(),
                $today->copy()->addDays($warningDays)
            ])
            ->orderBy('tanggal_kadaluarsa')
            ->take(5)
            ->get();

        return view('Admin.dashboard-admin', compact(
            'totalBarang',
            'barangMasukHariIni',
            'barangKeluarHariIni',
            'stokMenipis',
            'barangExpired',
            'barangHampirExpired',
            'aktivitasHariIni',
            'stokKritis',
            'expiredItems',
            'hampirExpiredItems'
        ));
    }
}

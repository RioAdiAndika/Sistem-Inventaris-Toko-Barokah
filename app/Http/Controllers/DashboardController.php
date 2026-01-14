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

        $stokMenipis = Product::whereRaw(
            '(SELECT COALESCE(SUM(jumlah),0) FROM barang_masuk WHERE product_id = products.id)
           - (SELECT COALESCE(SUM(jumlah),0) FROM barang_keluar WHERE product_id = products.id)
           <= stok_minimal'
        )->count();

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
        $stokKritis = Product::whereRaw(
            '(SELECT COALESCE(SUM(jumlah),0) FROM barang_masuk WHERE product_id = products.id)
           - (SELECT COALESCE(SUM(jumlah),0) FROM barang_keluar WHERE product_id = products.id)
           <= stok_minimal'
        )->get();

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

        return view('admin.dashboard-admin', compact(
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

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

        // 🔥 BARANG MASUK HARI INI
        $barangMasukHariIni = BarangMasuk::whereDate('tanggal', $today)->sum('jumlah');

        // BARANG KELUAR HARI INI
        $barangKeluarHariIni = BarangKeluar::whereDate('tanggal', $today)->sum('jumlah');

        // =====================
        // STOK MENIPIS
        // =====================
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
        // AKTIVITAS TERBARU
        // =====================
        $aktivitas = collect([
            ...BarangMasuk::with('product')->latest()->take(3)->get()->map(fn ($x) => [
                'text' => "Barang masuk: {$x->product->nama_barang}",
                'tanggal' => $x->created_at
            ]),
            ...BarangKeluar::with('product')->latest()->take(3)->get()->map(fn ($x) => [
                'text' => "Barang keluar: {$x->product->nama_barang}",
                'tanggal' => $x->created_at
            ]),
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
        $expiredItems = BarangMasuk::with('product')
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', $today)
            ->orderBy('tanggal_kadaluarsa')
            ->take(5)
            ->get();

        $hampirExpiredItems = BarangMasuk::with('product')
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
            'aktivitas',
            'stokKritis',
            'expiredItems',
            'hampirExpiredItems'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;

class DashboardGudangController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $warningDays = 90; // 3 bulan

        // =====================
        // STATISTIK UTAMA
        // =====================
        $totalBarang = Product::count();
        $totalMasuk  = BarangMasuk::sum('jumlah');
        $totalKeluar = BarangKeluar::sum('jumlah');

        // =====================
        // STOK MENIPIS
        // =====================
        $stokMenipis = Product::whereRaw(
            '(SELECT COALESCE(SUM(jumlah),0) FROM barang_masuk WHERE product_id = products.id) -
             (SELECT COALESCE(SUM(jumlah),0) FROM barang_keluar WHERE product_id = products.id)
             <= stok_minimal'
        )->count();

        // =====================
        // HITUNG EXPIRED
        // =====================
        $barangExpired = BarangMasuk::whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', $today)
            ->count();

        $barangHampirExpired = BarangMasuk::whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '>', $today)
            ->whereDate('tanggal_kadaluarsa', '<=', $today->copy()->addDays($warningDays))
            ->count();

        // =====================
        // STOK KRITIS DETAIL
        // =====================
        $stokKritis = Product::whereRaw(
            '(SELECT COALESCE(SUM(jumlah),0) FROM barang_masuk WHERE product_id = products.id) -
             (SELECT COALESCE(SUM(jumlah),0) FROM barang_keluar WHERE product_id = products.id)
             <= stok_minimal'
        )->get();

        // =====================
        // DETAIL BARANG EXPIRED
        // =====================
        $expiredItems = BarangMasuk::with('product')
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', $today)
            ->orderBy('tanggal_kadaluarsa')
            ->take(5)
            ->get();

        // =====================
        // DETAIL HAMPIR EXPIRED
        // =====================
        $hampirExpiredItems = BarangMasuk::with('product')
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '>', $today)
            ->whereDate('tanggal_kadaluarsa', '<=', $today->copy()->addDays($warningDays))
            ->orderBy('tanggal_kadaluarsa')
            ->take(5)
            ->get();

        return view('gudang.dashboard-gudang', compact(
            'totalBarang',
            'totalMasuk',
            'totalKeluar',
            'stokMenipis',
            'barangHampirExpired',
            'barangExpired',
            'stokKritis',
            'hampirExpiredItems',
            'expiredItems'
        ));
    }
}

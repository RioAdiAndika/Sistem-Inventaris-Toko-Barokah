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
        $today = Carbon::today();
        $warningDays = 90; // 3 bulan

        $barangExpired = BarangMasuk::whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<', $today)
            ->count();

        $barangHampirExpired = BarangMasuk::whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '>=', $today)
            ->whereDate('tanggal_kadaluarsa', '<=', $today->copy()->addDays($warningDays))
            ->count();

        // =====================
        // AKTIVITAS TERBARU
        // =====================
        $aktivitas = collect([
            ...BarangMasuk::with('product')->latest()->take(3)->get()->map(fn ($x) => [
                'text' => "Barang masuk: {$x->product->nama_barang}",
                'tanggal' => $x->tanggal
            ]),
            ...BarangKeluar::with('product')->latest()->take(3)->get()->map(fn ($x) => [
                'text' => "Barang keluar: {$x->product->nama_barang}",
                'tanggal' => $x->tanggal
            ]),
        ])->sortByDesc('tanggal')->take(5);

        // =====================
        // STOK KRITIS DETAIL
        // =====================
        $stokKritis = Product::whereRaw(
            '(SELECT COALESCE(SUM(jumlah),0) FROM barang_masuk WHERE product_id = products.id) -
             (SELECT COALESCE(SUM(jumlah),0) FROM barang_keluar WHERE product_id = products.id)
             <= stok_minimal'
        )->get();

        return view('admin.dashboard-admin', compact(
            'totalBarang',
            'totalMasuk',
            'totalKeluar',
            'stokMenipis',
            'barangHampirExpired',
            'barangExpired',
            'aktivitas',
            'stokKritis'
        ));
    }
}

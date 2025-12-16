<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalBarang = Product::count();
        $totalMasuk  = BarangMasuk::sum('jumlah');
        $totalKeluar = BarangKeluar::sum('jumlah');

        $stokMenipis = Product::whereRaw(
            '(SELECT COALESCE(SUM(jumlah),0) FROM barang_masuk WHERE product_id = products.id) -
             (SELECT COALESCE(SUM(jumlah),0) FROM barang_keluar WHERE product_id = products.id)
             <= stok_minimal'
        )->count();

        $aktivitas = collect([
            ...BarangMasuk::latest()->take(3)->get()->map(fn($x) => [
                'text' => "Barang masuk: {$x->product->nama_barang}",
                'tanggal' => $x->tanggal
            ]),
            ...BarangKeluar::latest()->take(3)->get()->map(fn($x) => [
                'text' => "Barang keluar: {$x->product->nama_barang}",
                'tanggal' => $x->tanggal
            ]),
        ])->sortByDesc('tanggal')->take(5);

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
            'aktivitas',
            'stokKritis'
        ));
    }
}

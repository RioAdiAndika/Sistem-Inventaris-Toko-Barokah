<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;

class GudangDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Total produk
        $totalBarang = Product::count();

        // Barang masuk hari ini
        $barangMasukHariIni = BarangMasuk::whereDate('tanggal', $today)
            ->sum('jumlah');

        // Barang keluar hari ini
        $barangKeluarHariIni = BarangKeluar::whereDate('tanggal', $today)
            ->sum('jumlah');

        return view('gudang.dashboard', compact(
            'totalBarang',
            'barangMasukHariIni',
            'barangKeluarHariIni'
        ));
    }
}

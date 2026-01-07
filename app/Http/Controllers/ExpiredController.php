<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use Carbon\Carbon;

class ExpiredController extends Controller
{
    /**
     * Barang Hampir Expired (≤ 90 hari)
     */
    public function hampir()
    {
        $today = Carbon::today();
        $warningDays = 90;

        $items = BarangMasuk::with('product')
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '>', $today)
            ->whereDate('tanggal_kadaluarsa', '<=', $today->copy()->addDays($warningDays))
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();

        return view('expired.hampir', compact('items'));
    }

    /**
     * Barang Sudah Expired
     */
    public function sudah()
    {
        $today = Carbon::today();

        $items = BarangMasuk::with('product')
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', $today)
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();

        return view('expired.sudah', compact('items'));
    }
}

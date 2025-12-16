<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Daftar kategori
        $kategoriOptions = [
            'Beras', 'Gula', 'Minyak Goreng', 'Tepung', 'Mie Instan',
            'Susu', 'Kopi & Teh', 'Biskuit & Snack', 'Rokok',
            'Obat-Obatan', 'Air Minum', 'Sabun & Deterjen', 'Lain-lain'
        ];

        // Filter kategori & search produk
        $productsQuery = Product::query();
        if ($request->kategori) {
            $productsQuery->where('kategori', $request->kategori);
        }
        if ($request->search) {
            $productsQuery->where('nama_barang', 'like', '%'.$request->search.'%');
        }
        $products = $productsQuery->get();

        // Top 5 barang masuk
        $barangMasukTerbanyak = BarangMasuk::selectRaw('product_id, SUM(jumlah) as total_masuk')
            ->groupBy('product_id')
            ->orderByDesc('total_masuk')
            ->take(5)
            ->with('product')
            ->get();

        // Top 5 barang keluar
        $barangKeluarTerbanyak = BarangKeluar::selectRaw('product_id, SUM(jumlah) as total_keluar')
            ->groupBy('product_id')
            ->orderByDesc('total_keluar')
            ->take(5)
            ->with('product')
            ->get();

        return view('laporan.index', compact(
            'products',
            'kategoriOptions',
            'barangMasukTerbanyak',
            'barangKeluarTerbanyak'
        ));
    }

    // Export CSV
    public function exportCsv()
    {
        $filename = 'laporan_inventaris_'.date('Ymd_His').'.csv';
        $products = Product::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kode', 'Nama', 'Kategori', 'Stok', 'Stok Minimal', 'Tanggal Dibuat']);

            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->kode_barang,
                    $p->nama_barang,
                    $p->kategori,
                    $p->stok,
                    $p->stok_minimal,
                    $p->created_at
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Export PDF
    public function exportPdf()
    {
        $products = Product::all();
        $pdf = Pdf::loadView('laporan.pdf', compact('products'));
        return $pdf->download('laporan_inventaris_'.date('Ymd_His').'.pdf');
    }
}

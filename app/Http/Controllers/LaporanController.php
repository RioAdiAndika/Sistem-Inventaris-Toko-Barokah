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
            'Beras',
            'Gula',
            'Minyak Goreng',
            'Tepung',
            'Mie Instan',
            'Susu',
            'Kopi & Teh',
            'Biskuit & Snack',
            'Rokok',
            'Obat-Obatan',
            'Air Minum',
            'Sabun & Deterjen',
            'Lain-lain'
        ];

        // Filter kategori & search produk
        $productsQuery = Product::query();
        if ($request->kategori) {
            $productsQuery->where('kategori', $request->kategori);
        }
        if ($request->search) {
            $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
        }
        $products = $productsQuery->get();

        // Ambil list product_id yang difilter
        $productIds = $products->pluck('id');

        // Top 5 Barang Masuk sesuai filter
        $barangMasukTerbanyak = BarangMasuk::selectRaw('product_id, SUM(jumlah) as total_masuk')
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->orderByDesc('total_masuk')
            ->take(5)
            ->with('product')
            ->get();

        // Top 5 Barang Keluar sesuai filter
        $barangKeluarTerbanyak = BarangKeluar::selectRaw('product_id, SUM(jumlah) as total_keluar')
            ->whereIn('product_id', $productIds)
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
    public function exportCsv(Request $request)
    {
        $filename = 'laporan_inventaris_' . date('Ymd_His') . '.csv';

        // 1️⃣ Filter produk sama seperti di index
        $productsQuery = Product::query();
        if ($request->kategori) {
            $productsQuery->where('kategori', $request->kategori);
        }
        if ($request->search) {
            $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
        }
        $products = $productsQuery->get();

        // Ambil list product_id yang difilter
        $productIds = $products->pluck('id');

        // 2️⃣ Top 5 Barang Masuk sesuai filter
        $barangMasukTerbanyak = BarangMasuk::selectRaw('product_id, SUM(jumlah) as total_masuk')
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->orderByDesc('total_masuk')
            ->take(5)
            ->with('product')
            ->get();

        // 3️⃣ Top 5 Barang Keluar sesuai filter
        $barangKeluarTerbanyak = BarangKeluar::selectRaw('product_id, SUM(jumlah) as total_keluar')
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->orderByDesc('total_keluar')
            ->take(5)
            ->with('product')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($products, $barangMasukTerbanyak, $barangKeluarTerbanyak) {
            $handle = fopen('php://output', 'w');

            // 1️⃣ Semua Produk
            fputcsv($handle, ['--- Semua Produk ---']);
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

            // 2️⃣ Top 5 Barang Masuk
            fputcsv($handle, []);
            fputcsv($handle, ['--- Barang Masuk Terbanyak (Top 5) ---']);
            fputcsv($handle, ['Nama Barang', 'Kategori', 'Total Masuk']);
            foreach ($barangMasukTerbanyak as $bm) {
                fputcsv($handle, [
                    $bm->product->nama_barang,
                    $bm->product->kategori,
                    $bm->total_masuk
                ]);
            }

            // 3️⃣ Top 5 Barang Keluar
            fputcsv($handle, []);
            fputcsv($handle, ['--- Barang Keluar Terbanyak (Top 5) ---']);
            fputcsv($handle, ['Nama Barang', 'Kategori', 'Total Keluar']);
            foreach ($barangKeluarTerbanyak as $bk) {
                fputcsv($handle, [
                    $bk->product->nama_barang,
                    $bk->product->kategori,
                    $bk->total_keluar
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }


    public function exportPdf(Request $request)
    {
        $productsQuery = Product::query();
        if ($request->kategori) {
            $productsQuery->where('kategori', $request->kategori);
        }
        if ($request->search) {
            $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
        }
        $products = $productsQuery->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('products'));
        return $pdf->download('laporan_inventaris_' . date('Ymd_His') . '.pdf');
    }
}

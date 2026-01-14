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
    $kategoriOptions = [
        'Beras','Gula','Minyak Goreng','Tepung','Mie Instan',
        'Susu','Kopi & Teh','Biskuit & Snack','Rokok','Obat-Obatan',
        'Air Minum','Sabun & Deterjen','Lain-lain'
    ];

    // Filter produk
    $productsQuery = Product::with(['satuans']); // eager load satuan
    if ($request->kategori) {
        $productsQuery->where('kategori', $request->kategori);
    }
    if ($request->search) {
        $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
    }
    $products = $productsQuery->get();

    // Hitung stok per produk per satuan
    $products = $products->map(function($p) {
        $p->stok_detail = $p->satuans->map(function($s) use ($p) {
            $masuk = BarangMasuk::where('product_id', $p->id)
                ->where('satuan_id', $s->id)
                ->sum('jumlah');

            $keluar = BarangKeluar::where('product_id', $p->id)
                ->where('satuan_id', $s->id)
                ->sum('jumlah');

            return [
                'satuan' => $s->nama,
                'stok' => $masuk - $keluar,
            ];
        });

        return $p;
    });

    // Top 5 Barang Masuk
    $barangMasukTerbanyak = BarangMasuk::selectRaw('product_id, satuan_id, SUM(jumlah) as total_masuk')
        ->groupBy('product_id','satuan_id')
        ->orderByDesc('total_masuk')
        ->take(5)
        ->with(['product','satuan'])
        ->get();

    // Top 5 Barang Keluar
    $barangKeluarTerbanyak = BarangKeluar::selectRaw('product_id, satuan_id, SUM(jumlah) as total_keluar')
        ->groupBy('product_id','satuan_id')
        ->orderByDesc('total_keluar')
        ->take(5)
        ->with(['product','satuan'])
        ->get();

    return view('laporan.index', compact(
        'products',
        'kategoriOptions',
        'barangMasukTerbanyak',
        'barangKeluarTerbanyak'
    ));
}

public function exportCsv(Request $request)
{
    $filename = 'laporan_inventaris_' . date('Ymd_His') . '.csv';

    // Filter produk sama seperti di index
    $productsQuery = Product::with(['satuans']);
    if ($request->kategori) {
        $productsQuery->where('kategori', $request->kategori);
    }
    if ($request->search) {
        $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
    }
    $products = $productsQuery->get();

    // Hitung stok per produk per satuan
    $products = $products->map(function($p) {
        $p->stok_detail = $p->satuans->map(function($s) use ($p) {
            $masuk = BarangMasuk::where('product_id', $p->id)
                ->where('satuan_id', $s->id)
                ->sum('jumlah');

            $keluar = BarangKeluar::where('product_id', $p->id)
                ->where('satuan_id', $s->id)
                ->sum('jumlah');

            return [
                'satuan' => $s->nama,
                'stok' => $masuk - $keluar,
            ];
        });
        return $p;
    });

    // Top 5 Barang Masuk
    $barangMasukTerbanyak = BarangMasuk::selectRaw('product_id, satuan_id, SUM(jumlah) as total_masuk')
        ->groupBy('product_id','satuan_id')
        ->orderByDesc('total_masuk')
        ->take(5)
        ->with(['product','satuan'])
        ->get();

    // Top 5 Barang Keluar
    $barangKeluarTerbanyak = BarangKeluar::selectRaw('product_id, satuan_id, SUM(jumlah) as total_keluar')
        ->groupBy('product_id','satuan_id')
        ->orderByDesc('total_keluar')
        ->take(5)
        ->with(['product','satuan'])
        ->get();

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function () use ($products, $barangMasukTerbanyak, $barangKeluarTerbanyak) {
        $handle = fopen('php://output', 'w');

        // Semua Produk
        fputcsv($handle, ['--- Semua Produk ---']);
        fputcsv($handle, ['Kode', 'Nama', 'Kategori', 'Stok / Satuan', 'Stok Minimal', 'Tanggal Dibuat']);
        foreach ($products as $p) {
            $stokText = $p->stok_detail->map(fn($sd) => $sd['stok'].' '.$sd['satuan'])->implode(', ');
            fputcsv($handle, [
                $p->kode_barang,
                $p->nama_barang,
                $p->kategori,
                $stokText,
                $p->stok_minimal,
                $p->created_at
            ]);
        }

        // Top 5 Barang Masuk
        fputcsv($handle, []);
        fputcsv($handle, ['--- Barang Masuk Terbanyak (Top 5) ---']);
        fputcsv($handle, ['Nama Barang', 'Satuan', 'Total Masuk']);
        foreach ($barangMasukTerbanyak as $bm) {
            fputcsv($handle, [
                $bm->product->nama_barang,
                $bm->satuan->nama,
                $bm->total_masuk
            ]);
        }

        // Top 5 Barang Keluar
        fputcsv($handle, []);
        fputcsv($handle, ['--- Barang Keluar Terbanyak (Top 5) ---']);
        fputcsv($handle, ['Nama Barang', 'Satuan', 'Total Keluar']);
        foreach ($barangKeluarTerbanyak as $bk) {
            fputcsv($handle, [
                $bk->product->nama_barang,
                $bk->satuan->nama,
                $bk->total_keluar
            ]);
        }

        fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
}

public function exportPdf(Request $request)
{
    $productsQuery = Product::with(['satuans']);
    if ($request->kategori) {
        $productsQuery->where('kategori', $request->kategori);
    }
    if ($request->search) {
        $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
    }
    $products = $productsQuery->get();

    // Hitung stok per satuan
    $products = $products->map(function($p) {
        $p->stok_detail = $p->satuans->map(function($s) use ($p) {
            $masuk = BarangMasuk::where('product_id', $p->id)
                ->where('satuan_id', $s->id)
                ->sum('jumlah');

            $keluar = BarangKeluar::where('product_id', $p->id)
                ->where('satuan_id', $s->id)
                ->sum('jumlah');

            return [
                'satuan' => $s->nama,
                'stok' => $masuk - $keluar,
            ];
        });
        return $p;
    });

    $pdf = Pdf::loadView('laporan.pdf', compact('products'));
    return $pdf->download('laporan_inventaris_' . date('Ymd_His') . '.pdf');
}
}

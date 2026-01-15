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

        // ================= FILTER PRODUK =================
        $productsQuery = Product::with('satuans');

        if ($request->kategori) {
            $productsQuery->where('kategori', $request->kategori);
        }

        if ($request->search) {
            $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        $products = $productsQuery->get();
        $productIds = $products->pluck('id');

        // ================= HITUNG STOK =================
        $products = $products->map(function ($p) {
            $p->stok_detail = $p->satuans
                ->map(function ($s) use ($p) {
                    $masuk = BarangMasuk::where('product_id', $p->id)
                        ->where('satuan_id', $s->id)
                        ->sum('jumlah');

                    $keluar = BarangKeluar::where('product_id', $p->id)
                        ->where('satuan_id', $s->id)
                        ->sum('jumlah');

                    return [
                        'satuan' => $s->nama,
                        'stok'   => $masuk - $keluar,
                    ];
                })
                ->filter(fn ($item) => $item['stok'] > 0)
                ->values();

            return $p;
        });

        // ================= TOP 5 BARANG MASUK =================
        $barangMasukTerbanyak = BarangMasuk::selectRaw(
                'product_id, satuan_id, SUM(jumlah) as total_masuk'
            )
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id', 'satuan_id')
            ->orderByDesc('total_masuk')
            ->take(5)
            ->with(['product', 'satuan'])
            ->get();

        // ================= TOP 5 BARANG KELUAR =================
        $barangKeluarTerbanyak = BarangKeluar::selectRaw(
                'product_id, satuan_id, SUM(jumlah) as total_keluar'
            )
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id', 'satuan_id')
            ->orderByDesc('total_keluar')
            ->take(5)
            ->with(['product', 'satuan'])
            ->get();

        return view('laporan.index', compact(
            'products',
            'kategoriOptions',
            'barangMasukTerbanyak',
            'barangKeluarTerbanyak'
        ));
    }

    // ================= EXPORT CSV =================
    public function exportCsv(Request $request)
    {
        $filename = 'laporan_inventaris_' . date('Ymd_His') . '.csv';

        $productsQuery = Product::with('satuans');

        if ($request->kategori) {
            $productsQuery->where('kategori', $request->kategori);
        }

        if ($request->search) {
            $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        $products = $productsQuery->get();
        $productIds = $products->pluck('id');

        $products = $products->map(function ($p) {
            $p->stok_detail = $p->satuans->map(function ($s) use ($p) {
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

        $barangMasukTerbanyak = BarangMasuk::selectRaw(
                'product_id, satuan_id, SUM(jumlah) as total_masuk'
            )
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id','satuan_id')
            ->orderByDesc('total_masuk')
            ->take(5)
            ->with(['product','satuan'])
            ->get();

        $barangKeluarTerbanyak = BarangKeluar::selectRaw(
                'product_id, satuan_id, SUM(jumlah) as total_keluar'
            )
            ->whereIn('product_id', $productIds)
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

            fputcsv($handle, ['--- Semua Produk ---']);
            fputcsv($handle, ['Kode', 'Nama', 'Kategori', 'Stok / Satuan', 'Stok Minimal', 'Tanggal Dibuat']);

            foreach ($products as $p) {
                $stokText = $p->stok_detail
                    ->map(fn ($sd) => $sd['stok'].' '.$sd['satuan'])
                    ->implode(', ');

                fputcsv($handle, [
                    $p->kode_barang,
                    $p->nama_barang,
                    $p->kategori,
                    $stokText,
                    $p->stok_minimal,
                    $p->created_at
                ]);
            }

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

    // ================= EXPORT PDF =================
    public function exportPdf(Request $request)
    {
        $productsQuery = Product::with('satuans');

        if ($request->kategori) {
            $productsQuery->where('kategori', $request->kategori);
        }

        if ($request->search) {
            $productsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        $products = $productsQuery->get();
        $productIds = $products->pluck('id');

        $products = $products->map(function ($p) {
            $p->stok_detail = $p->satuans->map(function ($s) use ($p) {
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

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangKeluarController extends Controller
{
    // =============================
    // INDEX
    // =============================
    public function index()
    {
        $data = BarangKeluar::with(['product', 'barangMasuk.satuan'])
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->tanggal_format = Carbon::parse($item->tanggal)->format('d-m-Y');
                return $item;
            });

        return view('barang_keluar.index', compact('data'));
    }

    // =============================
    // FORM CREATE
    // =============================
    public function create()
    {
        $products = Product::orderBy('nama_barang')->get();
        return view('barang_keluar.create', compact('products'));
    }

    // =============================
    // AJAX: BATCH PER PRODUCT
    // =============================
    public function getBatchByProduct(Product $product)
{
    $batches = BarangMasuk::with('barangKeluar', 'satuan')
        ->where('product_id', $product->id)
        ->orderBy('tanggal_kadaluarsa')
        ->get()
        ->groupBy(function ($item) {
            $tgl = $item->tanggal_kadaluarsa ?? 'tanpa_kadaluarsa';
            $satuan = $item->satuan_id ?? 'no_satuan';
            return $tgl . '_' . $satuan;
        })
        ->map(function ($group) {
            $totalStok = 0;
            $satuanNama = '';
            $satuanId = null;
            $tanggalKadaluarsa = null;
            $ids = [];

            foreach ($group as $item) {
                $stokKeluar = $item->barangKeluar->sum('jumlah');
                $sisa = $item->jumlah - $stokKeluar;

                if ($sisa > 0) {
                    $totalStok += $sisa;
                    $satuanNama = $item->satuan->nama ?? '';
                    $satuanId = $item->satuan_id;
                    $tanggalKadaluarsa = $item->tanggal_kadaluarsa;
                    $ids[] = $item->id; // simpan semua ID batch yang digabung
                }
            }

            if ($totalStok <= 0) return null;

            return [
                'stok' => $totalStok,
                'satuan' => $satuanNama,
                'satuan_id' => $satuanId,
                'tanggal_kadaluarsa' => $tanggalKadaluarsa
                    ? Carbon::parse($tanggalKadaluarsa)->format('d-m-Y')
                    : 'Tanpa Kadaluarsa',
                'ids' => $ids, // kirim array ID
            ];
        })
        ->filter()
        ->values();

    return response()->json($batches);
}


    // =============================
    // STORE
    // =============================
   public function store(Request $request)
{
    try {

        DB::transaction(function () use ($request) {

            $batchIds = explode(',', $request->barang_masuk_id);

            $batches = BarangMasuk::with('barangKeluar')
                ->whereIn('id', $batchIds)
                ->orderBy('tanggal_kadaluarsa')
                ->lockForUpdate()
                ->get();

            $totalStok = 0;

            foreach ($batches as $batch) {
                $stokKeluar = $batch->barangKeluar->sum('jumlah');
                $totalStok += ($batch->jumlah - $stokKeluar);
            }

            if ($request->jumlah > $totalStok) {
                throw new \Exception(
                    'Stok tidak mencukupi. Sisa stok: ' . $totalStok
                );
            }

            $sisa = $request->jumlah;

            foreach ($batches as $batch) {
                if ($sisa <= 0) break;

                $stokKeluar = $batch->barangKeluar->sum('jumlah');
                $tersedia = $batch->jumlah - $stokKeluar;

                if ($tersedia <= 0) continue;

                $ambil = min($tersedia, $sisa);

                BarangKeluar::create([
                    'product_id'      => $request->product_id,
                    'barang_masuk_id' => $batch->id,
                    'satuan_id'       => $request->satuan_id,
                    'jumlah'          => $ambil,
                    'tanggal'         => $request->tanggal,
                ]);

                $sisa -= $ambil;
            }
        });

        // ✅ SUKSES → PINDAH KE INDEX
        return redirect()
            ->route('barang-keluar.index')
            ->with('success', 'Barang keluar berhasil disimpan');

    } catch (\Exception $e) {

        // ❌ GAGAL → KEMBALI KE FORM
        return back()
            ->withErrors($e->getMessage())
            ->withInput();
    }
}
}

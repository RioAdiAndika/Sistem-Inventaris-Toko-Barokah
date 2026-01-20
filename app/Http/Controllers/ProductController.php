<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    /* =========================
     * DAFTAR PRODUK (STOK DINAMIS)
     * ========================= */
    public function index()
{
    $products = Product::with(['satuans'])->orderBy('nama_barang')->get();

    foreach ($products as $product) {

        $stokPerSatuan = BarangMasuk::with(['satuan', 'barangKeluar'])
            ->where('product_id', $product->id)
            ->get()
            ->groupBy('satuan_id')
            ->map(function ($items) {

                $totalMasuk = $items->sum('jumlah');

                $totalKeluar = $items->sum(function ($item) {
                    return $item->barangKeluar->sum('jumlah');
                });

                return (object) [
                    'satuan' => $items->first()->satuan->nama,
                    'stok'   => $totalMasuk - $totalKeluar
                ];
            })
            ->filter(fn ($item) => $item->stok > 0)
            ->values();

        $product->stok_per_satuan = $stokPerSatuan;
    }

    return view('products.index', compact('products'));
}


    /* =========================
     * FORM TAMBAH PRODUK
     * ========================= */
    public function create()
    {
        $kategoriOptions = $this->kategoriOptions();
        $satuans = Satuan::orderBy('nama')->get();

        return view('products.create', compact('kategoriOptions', 'satuans'));
    }

    /* =========================
     * SIMPAN PRODUK BARU
     * ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'  => 'required|unique:products',
            'nama_barang'  => 'required',
            'kategori'     => 'required',
            'stok_minimal' => 'required|integer|min:0',
            'satuan_ids'   => 'required|array|min:1',
            'satuan_ids.*' => 'exists:satuans,id',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        $data = $request->only([
            'kode_barang',
            'nama_barang',
            'kategori',
            'stok_minimal'
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->storeAs('products', $filename, 'public');
            $data['gambar'] = $filename;
        }

        $product = Product::create($data);

        if ($request->filled('satuan_ids')) {
            $product->satuans()->sync($request->satuan_ids);
        }


        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /* =========================
     * FORM EDIT PRODUK
     * ========================= */
    public function edit(Product $product)
{
    $kategoriOptions = $this->kategoriOptions();
    $satuans = Satuan::orderBy('nama')->get();

    // ambil id satuan yg dimiliki produk
    $selectedSatuans = $product->satuans->pluck('id')->toArray();

    return view('products.edit', compact(
        'product',
        'kategoriOptions',
        'satuans',
        'selectedSatuans'
    ));
}


    /* =========================
     * UPDATE PRODUK
     * ========================= */
    public function update(Request $request, Product $product)
{
    $request->validate([
        'kode_barang'  => 'required|unique:products,kode_barang,' . $product->id,
        'nama_barang'  => 'required',
        'kategori'     => 'required',
        'stok_minimal' => 'required|integer|min:0',
        'satuan_ids'   => 'required|array|min:1',
        'satuan_ids.*' => 'exists:satuans,id',
        'gambar'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = $request->only([
        'kode_barang',
        'nama_barang',
        'kategori',
        'stok_minimal'
    ]);

    if ($request->hasFile('gambar')) {
        if ($product->gambar && Storage::disk('public')->exists('products/' . $product->gambar)) {
            Storage::disk('public')->delete('products/' . $product->gambar);
        }

        $file = $request->file('gambar');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->storeAs('products', $filename, 'public');
        $data['gambar'] = $filename;
    }

    // update produk
    $product->update($data);

    // 🔥 update relasi satuan
    $product->satuans()->sync($request->satuan_ids);

    return redirect()
        ->route('products.index')
        ->with('success', 'Produk berhasil diperbarui');
}


    /* =========================
     * HAPUS PRODUK
     * ========================= */
    public function destroy(Product $product)
{
    $jumlahMasuk = BarangMasuk::where('product_id', $product->id)->count();
    $jumlahKeluar = BarangKeluar::where('product_id', $product->id)->count();

    if ($jumlahMasuk > 0 || $jumlahKeluar > 0) {
        return back()->with(
            'error',
            'Produk tidak bisa dihapus karena sudah memiliki transaksi'
        );
    }

    // hapus relasi satuan (pivot)
    $product->satuans()->detach();

    // hapus gambar
    if ($product->gambar && Storage::disk('public')->exists('products/' . $product->gambar)) {
        Storage::disk('public')->delete('products/' . $product->gambar);
    }

    $product->delete();

    return back()->with('success', 'Produk berhasil dihapus');
}


    /* =========================
     * KATEGORI STATIS
     * ========================= */
    private function kategoriOptions()
    {
        return [
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
        ];
    }
    public function expired($id)
    {
        $product = Product::findOrFail($id);

        $stock_per_exp = BarangMasuk::with('satuan', 'barangKeluar')
            ->where('product_id', $product->id)
            ->get()
            ->groupBy(function ($item) {
                return ($item->tanggal_kadaluarsa ?? 'null') . '|' . $item->satuan_id;
            })
            ->map(function ($group) {

                $totalMasuk = $group->sum('jumlah');

                $totalKeluar = $group->sum(function ($item) {
                    return $item->barangKeluar->sum('jumlah');
                });

                $sisa = $totalMasuk - $totalKeluar;

                return (object) [
                    'tanggal_kadaluarsa' => $group->first()->tanggal_kadaluarsa,
                    'satuan' => $group->first()->satuan->nama ?? '-',
                    'total_stok' => $sisa,
                ];
            })
            ->filter(fn($item) => $item->total_stok > 0)
            ->values();

        return view('products.expired', compact('product', 'stock_per_exp'));
    }


    public function katalog(Request $request)
{
    $query = Product::query();

    // 🔍 Search nama produk
    if ($request->filled('search')) {
        $query->where('nama_barang', 'like', '%' . $request->search . '%');
    }

    // 🗂️ Filter kategori
    if ($request->filled('kategori')) {
        $query->where('kategori', $request->kategori);
    }

    $products = $query
        ->orderBy('nama_barang')
        ->get()
        ->map(function ($product) {

            // 📦 Hitung stok per satuan
            $stokPerSatuan = BarangMasuk::with('satuan', 'barangKeluar')
                ->where('product_id', $product->id)
                ->get()
                ->groupBy('satuan_id')
                ->map(function ($group) {

                    $totalMasuk = $group->sum('jumlah');

                    $totalKeluar = $group->sum(function ($item) {
                        return $item->barangKeluar->sum('jumlah');
                    });

                    return (object) [
                        'satuan' => $group->first()->satuan->nama ?? '-',
                        'stok'   => $totalMasuk - $totalKeluar,
                    ];
                })
                ->filter(fn ($item) => $item->stok > 0)
                ->values();

            $product->stok_per_satuan = $stokPerSatuan;

            return $product;
        });

    $kategoriOptions = $this->kategoriOptions();

    return view('products.katalog', compact('products', 'kategoriOptions'));
}

}

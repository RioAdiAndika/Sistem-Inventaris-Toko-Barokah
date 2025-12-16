<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Tampilkan daftar semua produk
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    // Tampilkan form tambah barang
    public function create()
    {
        $kategoriOptions = $this->kategoriOptions();
        return view('products.create', compact('kategoriOptions'));
    }

    // Simpan barang baru
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:products',
            'nama_barang' => 'required',
            'kategori' => 'required',
            'stok_minimal' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nama_barang);
            $extension = $file->getClientOriginalExtension();
            $filename = $safeName . '_' . time() . '.' . $extension;

            $file->storeAs('products', $filename, 'public');
            $data['gambar'] = $filename;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // Tampilkan form edit barang
    public function edit(Product $product)
    {
        $kategoriOptions = $this->kategoriOptions();
        return view('products.edit', compact('product', 'kategoriOptions'));
    }

    // Update data barang
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'kode_barang' => 'required|unique:products,kode_barang,' . $product->id,
            'nama_barang' => 'required',
            'kategori' => 'required',
            'stok_minimal' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            // hapus file lama jika ada
            if ($product->gambar && Storage::disk('public')->exists('products/' . $product->gambar)) {
                Storage::disk('public')->delete('products/' . $product->gambar);
            }

            $file = $request->file('gambar');
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nama_barang);
            $extension = $file->getClientOriginalExtension();
            $filename = $safeName . '_' . time() . '.' . $extension;

            $file->storeAs('products', $filename, 'public');
            $data['gambar'] = $filename;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupdate!');
    }

    // Hapus barang
    public function destroy(Product $product)
    {
        if ($product->gambar && Storage::disk('public')->exists('products/' . $product->gambar)) {
            Storage::disk('public')->delete('products/' . $product->gambar);
        }

        $product->delete();
        return back()->with('success', 'Barang berhasil dihapus');
    }

    // Tampilkan katalog untuk role gudang dengan filter
    public function katalog(Request $request)
    {
        $query = Product::query();

        // Filter kategori jika ada
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        // Search nama produk
        if ($request->search) {
            $query->where('nama_barang', 'like', '%'.$request->search.'%');
        }

        $products = $query->orderBy('nama_barang')->get();
        $kategoriOptions = $this->kategoriOptions();

        return view('products.katalog', compact('products', 'kategoriOptions'));
    }

    // Daftar kategori
    private function kategoriOptions()
    {
        return [
            'Beras', 'Gula', 'Minyak Goreng', 'Tepung', 'Mie Instan',
            'Susu', 'Kopi & Teh', 'Biskuit & Snack', 'Rokok',
            'Obat-Obatan', 'Air Minum', 'Sabun & Deterjen', 'Lain-lain'
        ];
    }
}

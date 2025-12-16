<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $data = BarangKeluar::with('product')->latest()->get();
        return view('barang_keluar.index', compact('data'));
    }

    public function create()
    {
        $products = Product::all();
        return view('barang_keluar.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
        ]);

        $product = Product::findOrFail($request->product_id);

        // CEK STOK
        if ($request->jumlah > $product->stok) {
            return back()->withErrors(['jumlah' => 'Stok tidak mencukupi']);
        }

        BarangKeluar::create($request->all());

        return redirect()->route('barang-keluar.index')
            ->with('success', 'Barang keluar berhasil disimpan');
    }
}

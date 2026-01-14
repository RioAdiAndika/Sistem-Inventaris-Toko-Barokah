@extends('layouts.app')

@section('content')
<h4>Edit Barang</h4>

<form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Kode Barang</label>
        <input type="text" name="kode_barang" class="form-control" value="{{ $product->kode_barang }}">
    </div>
    <div class="mb-3">
        <label>Nama Barang</label>
        <input type="text" name="nama_barang" class="form-control" value="{{ $product->nama_barang }}">
    </div>
    <div class="mb-3">
    <label>Kategori</label>
    <select name="kategori" class="form-select">
        <option value="">-- Pilih Kategori --</option>
        @foreach($kategoriOptions as $k)
            <option value="{{ $k }}" {{ $product->kategori == $k ? 'selected' : '' }}>
                {{ $k }}
            </option>
        @endforeach
    </select>
    </div>

    <div class="mb-3">
        <label>Stok Minimal</label>
        <input type="number" name="stok_minimal" class="form-control" value="{{ $product->stok_minimal }}">
    </div>
    <div class="mb-3">
        <label>Satuan</label>
        <div class="row">
            @foreach ($satuans as $satuan)
                <div class="col-md-3">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="satuan_ids[]"
                            value="{{ $satuan->id }}"
                            {{ in_array($satuan->id, $selectedSatuans) ? 'checked' : '' }}
                        >
                        <label class="form-check-label">
                            {{ $satuan->nama }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-3">
    <label>Gambar Produk</label>
    <input type="file" name="gambar" class="form-control">

    @if($product->gambar && file_exists(storage_path('app/public/products/'.$product->gambar)))
        <img src="{{ asset('storage/products/'.$product->gambar) }}" width="100" class="mt-2" alt="{{ $product->nama_barang }}">
    @else
        <p class="text-muted mt-2">Belum ada gambar</p>
    @endif
</div>


    <button class="btn btn-primary">Update</button>
</form>
@endsection

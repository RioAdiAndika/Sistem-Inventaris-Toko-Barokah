@extends('layouts.app')

@section('content')
    <h4>Tambah Barang</h4>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control">
        </div>
        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control">
        </div>
        <div class="mb-3">
            <label>Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriOptions as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Satuan Barang</label>

            <div class="row">
                @forelse($satuans as $satuan)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="satuan_ids[]" value="{{ $satuan->id }}"
                                id="satuan{{ $satuan->id }}">

                            <label class="form-check-label" for="satuan{{ $satuan->id }}">
                                {{ $satuan->nama }}
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="text-danger">
                        ❌ Data satuan belum tersedia
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mb-3">
            <label>Stok Minimal</label>
            <input type="number" name="stok_minimal" class="form-control" value="0">
        </div>
        <div class="mb-3">
            <label>Gambar Produk</label>
            <input type="file" name="gambar" class="form-control">
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
@endsection

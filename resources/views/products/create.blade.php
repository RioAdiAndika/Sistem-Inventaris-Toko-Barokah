@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Tambah Barang</h4>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kode Barang</label>
            <input type="text" name="kode_barang"
                class="form-control @error('kode_barang') is-invalid @enderror"
                value="{{ old('kode_barang') }}">

            @error('kode_barang')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" name="nama_barang"
                class="form-control @error('nama_barang') is-invalid @enderror"
                value="{{ old('nama_barang') }}">

            @error('nama_barang')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori"
                class="form-select @error('kategori') is-invalid @enderror">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriOptions as $k)
                    <option value="{{ $k }}" {{ old('kategori') == $k ? 'selected' : '' }}>
                        {{ $k }}
                    </option>
                @endforeach
            </select>

            @error('kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Satuan Barang</label>
            <div class="row">
                @foreach ($satuans as $satuan)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                name="satuan_ids[]"
                                value="{{ $satuan->id }}"
                                {{ in_array($satuan->id, old('satuan_ids', [])) ? 'checked' : '' }}>

                            <label class="form-check-label">
                                {{ $satuan->nama }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            @error('satuan_ids')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Stok Minimal</label>
            <input type="number" name="stok_minimal"
                class="form-control @error('stok_minimal') is-invalid @enderror"
                value="{{ old('stok_minimal', 0) }}">

            @error('stok_minimal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar Produk</label>
            <input type="file" name="gambar"
                class="form-control @error('gambar') is-invalid @enderror">

            @error('gambar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary">
            💾 Simpan
        </button>
    </form>
@endsection

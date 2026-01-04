@extends('layouts.app')

@section('content')
<h4 class="mb-3"> Tambah Barang Masuk</h4>

<form action="{{ route('barang-masuk.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Nama Barang</label>
        <select name="product_id" class="form-select" required>
            <option value="">-- Pilih Barang --</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}">
                    {{ $p->kode_barang }} - {{ $p->nama_barang }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Jumlah Masuk</label>
        <input type="number" name="jumlah" class="form-control" min="1" required>
    </div>

    <div class="mb-3">
        <label>Tanggal Kadaluarsa</label>
        <input type="date" name="tanggal_kadaluarsa" class="form-control">
    </div>

    <div class="mb-3">
            <label>Tanggal Masuk</label>
            <input type="date" name="tanggal" class="form-control"
                   value="{{ date('Y-m-d') }}" required>
    </div>

    <button class="btn btn-success">Simpan</button>
    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection

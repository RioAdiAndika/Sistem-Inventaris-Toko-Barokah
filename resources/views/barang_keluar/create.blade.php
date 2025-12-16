@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Tambah Barang Keluar</h4>

    <form action="{{ route('barang-keluar.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Barang</label>
            <select name="product_id" class="form-control">
                @foreach($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->nama_barang }} (Stok: {{ $product->stok }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control"
                value="{{ old('tanggal', date('Y-m-d')) }}">
        </div>


        <button class="btn btn-danger">Simpan</button>
    </form>
</div>
@endsection

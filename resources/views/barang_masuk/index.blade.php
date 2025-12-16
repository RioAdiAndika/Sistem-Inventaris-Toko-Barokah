@extends('layouts.app')

@section('content')
<h4 class="mb-3">📥 Barang Masuk</h4>

<a href="{{ route('barang-masuk.create') }}" class="btn btn-primary mb-3">
    + Tambah Barang Masuk
</a>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->product->nama_barang }}</td>
            <td>{{ $item->jumlah }}</td>
            <td>{{ $item->tanggal }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

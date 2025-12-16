@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Barang Keluar</h4>

    <a href="{{ route('barang-keluar.create') }}" class="btn btn-danger mb-3">
        + Barang Keluar
    </a>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Tanggal</th>
        </tr>
        @foreach($data as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->product->nama_barang }}</td>
            <td>{{ $item->jumlah }}</td>
            <td>{{ $item->tanggal }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endsection

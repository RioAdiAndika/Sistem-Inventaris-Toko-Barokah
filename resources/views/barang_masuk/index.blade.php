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
            <th>Tanggal Kadaluarsa</th>
            <th>Tanggal</th>
            <th>Status Expired</th>
        </tr>
    </thead>
   <tbody>
@forelse ($data as $item)
    <tr class="{{ $item->row_class }}">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->product->nama_barang }}</td>
        <td>{{ $item->jumlah }}</td>
        <td>{{ $item->tanggal_kadaluarsa_format }}</td>
        <td>{{ $item->tanggal_format }}</td>
        <td>
            <span class="badge bg-{{ $item->badge }}">
                {{ $item->status_expired }}
            </span>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted">
            Belum ada data barang masuk
        </td>
    </tr>
@endforelse
</tbody>
</table>
@endsection

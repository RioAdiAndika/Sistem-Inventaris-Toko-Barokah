@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h4>📦 Data Barang Keluar</h4>
        <a href="{{ route('barang-keluar.create') }}" class="btn btn-danger">
            + Barang Keluar
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
            <tr>
                <th>No</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Tanggal Keluar</th>
            </tr>
        </thead>

        <tbody>
            @forelse($data as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->product->nama_barang }}</td>
                    <td class="text-center">{{ $item->jumlah }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-center">{{ $item->tanggal_format }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        Data barang keluar belum tersedia
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection

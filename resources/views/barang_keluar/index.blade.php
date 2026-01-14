@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">📤 Data Barang Keluar</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-light text-center">
            <tr>
                <th>No</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Tgl Keluar</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->product->nama_barang }}</td>
                <td class="text-center">{{ $item->jumlah }}</td>
                <td class="text-center">{{ $item->barangMasuk->satuan->nama }}</td>
                <td class="text-center">{{ $item->tanggal_format }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">
                    Belum ada data
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

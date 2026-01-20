@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h4>📤 Data Barang Keluar</h4>
        <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary">
            + Tambah Barang Keluar
        </a>
    </div>

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

                {{-- 🔥 INI YANG BENAR --}}
                <td class="text-center">
                    {{ optional($item->satuan)->nama }}
                </td>

                <td class="text-center">{{ $item->tanggal_format }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">
                    Data barang keluar belum tersedia
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>
@endsection

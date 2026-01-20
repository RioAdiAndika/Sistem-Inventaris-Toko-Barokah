@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>📥 Data Barang Masuk</h4>
    <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">
        + Tambah Barang Masuk
    </a>
</div>

<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Tanggal Kadaluarsa</th>
            <th>Tanggal Masuk</th>
            <th>Status Expired</th>
        </tr>
    </thead>

    <tbody>
    @forelse ($data as $item)
        <tr class="{{ $item->row_class }}">
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->product->nama_barang }}</td>
            <td>{{ $item->jumlah }}</td>

            {{-- 🔥 INI YANG BENAR --}}
            <td>
                {{ optional($item->satuan)->nama ?? 'Belum diset' }}
            </td>

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
            <td colspan="7" class="text-center text-muted">
                Belum ada data barang masuk
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
@endsection

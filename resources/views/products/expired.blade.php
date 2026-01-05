@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Stok dan Tanggal Kadaluarsa: {{ $product->nama_barang }}</h4>

    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">
    Kembali
</a>
    <table class="table table-bordered">
        <thead class="table-light text-center">
            <tr>
                <th>No</th>
                <th>Tanggal Kadaluarsa</th>
                <th>Stok Tersedia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stock_per_exp as $index => $item)
                <tr class="{{ $item->total_stok == 0 ? 'table-danger' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->tanggal_kadaluarsa ?? 'Tanpa Exp' }}</td>
                    <td class="text-center">{{ $item->total_stok }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Tidak ada stok tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

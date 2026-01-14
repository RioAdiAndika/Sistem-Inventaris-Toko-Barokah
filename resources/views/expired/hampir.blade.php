@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Barang Hampir Expired</h4>

    <table class="table table-bordered">
        <thead class="table-light text-center">
            <tr>
                <th>No</th>
                <th>Tanggal Kadaluarsa</th>
                <th>Status</th>
                <th>Stok Tersedia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr class="table-warning">
                    <td class="text-center">{{ $index + 1 }}</td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d-m-Y') }}
                    </td>

                    <td class="text-center">
                        <span class="badge bg-warning text-dark">
                            Hampir Expired
                        </span>
                    </td>

                    <td class="text-center">
                        {{ $item->jumlah }} {{ $item->satuan->nama ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Tidak ada barang hampir expired
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

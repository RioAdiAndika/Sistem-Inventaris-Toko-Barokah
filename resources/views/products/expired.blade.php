@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">
        Stok dan Tanggal Kadaluarsa: {{ $product->nama_barang }}
    </h4>

    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">
        Kembali
    </a>

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
            @forelse(
                $stock_per_exp
                    ->where('total_stok', '>', 0)
                    ->sortBy(fn ($item) => $item->tanggal_kadaluarsa ?? '9999-12-31')
                as $index => $item
            )
                @php
                    $today = \Carbon\Carbon::today();
                    $exp = $item->tanggal_kadaluarsa
                        ? \Carbon\Carbon::parse($item->tanggal_kadaluarsa)
                        : null;

                    $diffDays = $exp ? $today->diffInDays($exp, false) : null;

                    if ($diffDays !== null && $diffDays <= 0) {
                        $status = 'Expired';
                        $badge = 'danger';
                        $rowClass = 'table-danger';
                    } elseif ($diffDays !== null && $diffDays <= 90) {
                        $status = 'Hampir expired';
                        $badge = 'warning';
                        $rowClass = 'table-warning';
                    } else {
                        $status = 'Aman';
                        $badge = 'success';
                        $rowClass = '';
                    }
                @endphp

                <tr class="{{ $rowClass }}">
                    <td class="text-center">{{ $index + 1 }}</td>

                    <td class="text-center">
                        {{ $exp ? $exp->format('d-m-Y') : 'Tanpa Exp' }}
                    </td>

                    <td class="text-center">
                        <span class="badge bg-{{ $badge }}">
                            {{ $status }}
                        </span>
                    </td>

                    <td class="text-center">
                        {{ $item->total_stok }} {{ $item->satuan }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Tidak ada stok tersedia
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

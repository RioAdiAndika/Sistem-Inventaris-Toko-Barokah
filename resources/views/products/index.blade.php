@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Data Barang</h4>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Barang
    </a>
</div>

<table class="table table-bordered align-middle">
    <thead class="table-light text-center">
        <tr>
            <th>Kode</th>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th width="180">Aksi</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($products as $p)
        <tr>
            <td>{{ $p->kode_barang }}</td>

            <td>
                @if ($p->gambar)
                    <img src="{{ asset('storage/products/' . $p->gambar) }}"
                         width="80"
                         class="img-thumbnail"
                         alt="{{ $p->nama_barang }}">
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            <td>{{ $p->nama_barang }}</td>
            <td>{{ $p->kategori }}</td>

            {{-- STOK PER SATUAN --}}
            <td>
                @if ($p->stok_per_satuan->count())
                    <ul class="mb-0 ps-3">
                        @foreach ($p->stok_per_satuan as $stok)
                            <li>{{ $stok->stok }} {{ $stok->satuan }}</li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted">Stok kosong</span>
                @endif
            </td>

            {{-- AKSI --}}
            <td>
                <a href="{{ route('products.edit', $p->id) }}"
                   class="btn btn-warning btn-sm mb-1">
                    Edit
                </a>

                <a href="{{ route('products.expired', $p->id) }}"
                   class="btn btn-info btn-sm mb-1">
                    Lihat
                </a>

                {{-- HAPUS --}}
                @if ($p->stok_per_satuan->count() > 0)
                    <button class="btn btn-danger btn-sm mb-1"
                            disabled
                            title="Produk masih memiliki stok">
                        Hapus
                    </button>
                @else
                    <form action="{{ route('products.destroy', $p->id) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm(
                            'Produk akan dihapus PERMANEN.\nPastikan tidak memiliki transaksi.\n\nLanjutkan?'
                          )">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm mb-1">
                            Hapus
                        </button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted">
                Data produk belum tersedia
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
@endsection

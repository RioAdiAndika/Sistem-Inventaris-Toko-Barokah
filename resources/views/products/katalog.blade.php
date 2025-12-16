@extends('layouts.app')

@section('content')
<h4>Katalog Produk</h4>

<div class="card mb-3 p-3">
    <form method="GET" class="row g-3 align-items-center">
        <div class="col-auto">
            <input type="text" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ request('search') }}">
        </div>
        <div class="col-auto">
            <select name="kategori" class="form-select">
                <option value="">-- Semua Kategori --</option>
                @foreach ($kategoriOptions as $k)
                    <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
            <a href="{{ route('products.katalog') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Kode</th>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $p)
        <tr>
            <td>{{ $p->kode_barang }}</td>
            <td>
                @if ($p->gambar)
                    <img src="{{ asset('storage/products/'.$p->gambar) }}" width="80" alt="{{ $p->nama_barang }}">
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>{{ $p->nama_barang }}</td>
            <td>{{ $p->kategori }}</td>
            <td>{{ $p->stok }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center text-muted">Tidak ada produk</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection

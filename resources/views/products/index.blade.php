@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>Data Barang</h4>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Kode</th>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
       <tbody>
    @foreach ($products as $p)
        <tr>
            <td>{{ $p->kode_barang }}</td>
            <td>
                @if ($p->gambar)
                    <img src="{{ asset('storage/products/' . $p->gambar) }}" width="80" alt="{{ $p->nama_barang }}">
                @else
                    -
                @endif
            </td>
            <td>{{ $p->nama_barang }}</td>
            <td>{{ $p->kategori }}</td>
            <td>{{ $p->stok }}</td>
            <td>
                <a href="{{ route('products.edit', $p->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('products.destroy', $p->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Hapus barang?')" class="btn btn-danger btn-sm">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>

    </table>
@endsection

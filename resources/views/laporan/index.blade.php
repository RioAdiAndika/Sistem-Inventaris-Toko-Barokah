@extends('layouts.app')

@section('content')
<h4>Laporan Inventaris</h4>

{{-- Filter --}}
<form method="GET" class="mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <select name="kategori" class="form-select">
                <option value="">-- Semua Kategori --</option>
                @foreach($kategoriOptions as $k)
                    <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari nama barang..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary">Filter</button>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </div>
</form>

{{-- Tombol Export --}}
<div class="mb-3">
    <a href="{{ route('laporan.exportCsv', request()->query()) }}" class="btn btn-success">Export CSV</a>
    <a href="{{ route('laporan.exportPdf', request()->query()) }}" class="btn btn-danger">Download PDF</a>
</div>

{{-- Tabel Semua Produk --}}
<div class="card mb-4">
    <div class="card-header bg-light">Semua Produk</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok / Satuan</th>
                    <th>Stok Minimal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    <tr>
                        <td>{{ $p->kode_barang }}</td>
                        <td>
                            @if($p->gambar)
                                <img src="{{ asset('storage/products/'.$p->gambar) }}" width="50" alt="{{ $p->nama_barang }}">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $p->nama_barang }}</td>
                        <td>{{ $p->kategori }}</td>
                        <td>
                            @foreach($p->stok_detail as $sd)
                                {{ $sd['stok'] }} {{ $sd['satuan'] }}<br>
                            @endforeach
                        </td>
                        <td>{{ $p->stok_minimal }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Barang Masuk Terbanyak --}}
<div class="card mb-4">
    <div class="card-header bg-light">Barang Masuk Terbanyak (Top 5)</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Total Masuk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangMasukTerbanyak as $bm)
                    <tr>
                        <td>{{ $bm->product->nama_barang }}</td>
                        <td>{{ $bm->satuan->nama }}</td>
                        <td>{{ $bm->total_masuk }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Barang Keluar Terbanyak --}}
<div class="card mb-4">
    <div class="card-header bg-light">Barang Keluar Terbanyak (Top 5)</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Total Keluar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangKeluarTerbanyak as $bk)
                    <tr>
                        <td>{{ $bk->product->nama_barang }}</td>
                        <td>{{ $bk->satuan->nama }}</td>
                        <td>{{ $bk->total_keluar }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


@endsection

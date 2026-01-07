@extends('layouts.app')

@section('content')
    <div class="container-fluid p-4 bg-light min-vh-100">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Dashboard Admin</h2>
                <small class="text-muted">Sistem Inventaris Gudang</small>
            </div>
        </div>

        <!-- STATISTIK -->
        <div class="row g-4 mb-4">

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Barang</p>
                            <h4 class="fw-bold">{{ $totalBarang }}</h4>
                        </div>
                        <i class="bi bi-box-seam fs-2 text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Barang Masuk</p>
                            <h4 class="fw-bold">{{ $totalMasuk }}</h4>
                        </div>
                        <i class="bi bi-arrow-down-square fs-2 text-success"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Stok Menipis</p>
                            <h4 class="fw-bold text-warning">{{ $stokMenipis }}</h4>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-sm-6">
                <a href="{{ route('expired.hampir') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 hover-shadow">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Hampir Expired</p>
                                <h4 class="fw-bold text-warning">
                                    {{ $barangHampirExpired }}
                                </h4>
                            </div>
                            <i class="bi bi-clock-history fs-2 text-warning"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 col-sm-6">
                <a href="{{ route('expired.sudah') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 hover-shadow">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Sudah Expired</p>
                                <h4 class="fw-bold text-danger">
                                    {{ $barangExpired }}
                                </h4>
                            </div>
                            <i class="bi bi-x-octagon fs-2 text-danger"></i>
                        </div>
                    </div>
                </a>
            </div>

        </div>
        <!-- CONTENT -->
        <div class="row g-4">

            <!-- AKTIVITAS -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-semibold">
                        Aktivitas Terbaru
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($aktivitas as $item)
                                <li class="list-group-item d-flex justify-content-between">
                                    {{ $item['text'] }}
                                    <span class="text-muted small">
                                        {{ $item['tanggal']->diffForHumans() }}
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">
                                    Belum ada aktivitas
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <!-- STOK KRITIS -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-semibold">
                        Stok Hampir Habis
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Barang</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokKritis as $p)
                                    <tr>
                                        <td>{{ $p->nama_barang }}</td>
                                        <td>{{ $p->stok }}</td>
                                        <td>
                                            <span
                                                class="badge
                                            {{ $p->stok <= 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                {{ $p->stok <= 0 ? 'Habis' : 'Menipis' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            Semua stok aman
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-semibold text-warning">
                        Hampir Expired
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Barang</th>
                                    <th>Tgl Exp</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hampirExpiredItems as $item)
                                    <tr class="table-warning">
                                        <td>{{ $item->product->nama_barang }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d-m-Y') }}</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                Hampir Expired
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            Tidak ada barang hampir expired
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-semibold text-danger">
                        Barang Expired
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Barang</th>
                                    <th>Tgl Exp</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiredItems as $item)
                                    <tr class="table-danger">
                                        <td>{{ $item->product->nama_barang }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d-m-Y') }}</td>
                                        <td>
                                            <span class="badge bg-danger">Expired</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            Tidak ada barang expired
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

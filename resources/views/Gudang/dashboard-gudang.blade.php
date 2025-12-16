@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Dashboard Gudang</h2>
        <small class="text-muted">Petugas Gudang</small>
    </div>
</div>

<!-- STATISTIK -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <p class="text-muted mb-1">Total Barang</p>
                <h3 class="fw-bold">120</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <p class="text-muted mb-1">Barang Masuk Hari Ini</p>
                <h3 class="fw-bold text-success">12</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <p class="text-muted mb-1">Barang Keluar Hari Ini</p>
                <h3 class="fw-bold text-danger">8</h3>
            </div>
        </div>
    </div>
</div>

<!-- AKSI UTAMA -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <i class="bi bi-arrow-down-square fs-1 text-success"></i>
                <h5 class="mt-3">Barang Masuk</h5>
                <p class="text-muted">Catat barang yang masuk ke gudang</p>
                <a href="#" class="btn btn-success">Input Barang Masuk</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <i class="bi bi-arrow-up-square fs-1 text-danger"></i>
                <h5 class="mt-3">Barang Keluar</h5>
                <p class="text-muted">Catat barang yang keluar dari gudang</p>
                <a href="#" class="btn btn-danger">Input Barang Keluar</a>
            </div>
        </div>
    </div>
</div>

@endsection

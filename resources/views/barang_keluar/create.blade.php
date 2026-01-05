@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Tambah Barang Keluar</h4>

    {{-- ALERT --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('barang-keluar.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Barang</label>
            <select name="product_id" id="product_id" class="form-select" required>
                <option value="">-- Pilih Barang --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->nama_barang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal Kadaluarsa</label>
            <select name="tanggal_kadaluarsa" id="tanggal_kadaluarsa" class="form-select" required>
                <option value="">-- Pilih Barang Terlebih Dahulu --</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Keluar</label>
            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
        </div>

        <button class="btn btn-danger">Simpan</button>
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
    const stockPerExp = @json($stock_per_exp);

    document.getElementById('product_id').addEventListener('change', function () {
        const select = document.getElementById('tanggal_kadaluarsa');
        select.innerHTML = '<option value="">-- Pilih Tanggal Kadaluarsa --</option>';

        if (stockPerExp[this.value]) {
            stockPerExp[this.value].forEach(item => {
                if (item.total_stok > 0) {
                    const option = document.createElement('option');
                    option.value = item.tanggal_kadaluarsa;
                    option.text = item.tanggal_kadaluarsa + ' (Stok: ' + item.total_stok + ')';
                    select.appendChild(option);
                }
            });
        }
    });
</script>
@endsection

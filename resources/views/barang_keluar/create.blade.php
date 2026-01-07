@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Tambah Barang Keluar</h4>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('barang-keluar.store') }}" method="POST">
        @csrf

        {{-- BARANG --}}
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

        {{-- TANGGAL KADALUARSA --}}
        <div class="mb-3">
            <label>Tanggal Kadaluarsa</label>
            <select id="tanggal_kadaluarsa" class="form-select" required>
                <option value="">-- Pilih Barang Terlebih Dahulu --</option>
            </select>
        </div>

        {{-- HIDDEN BARANG MASUK --}}
        <input type="hidden" name="barang_masuk_id" id="barang_masuk_id">

        {{-- JUMLAH --}}
        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>

        {{-- SATUAN --}}
        <div class="mb-3">
            <label>Satuan</label>
            <select name="satuan" id="satuan" class="form-select" required>
                <option value="">-- Pilih Satuan --</option>
                <option value="Dus">Dus</option>
                <option value="Slop">Slop</option>
                <option value="Karung">Karung</option>
                <option value="Pack">Pack</option>
                <option value="Botol">Botol</option>
                <option value="Pcs">Pcs</option>
            </select>
        </div>

        {{-- TANGGAL KELUAR --}}
        <div class="mb-3">
            <label>Tanggal Keluar</label>
            <input type="date" name="tanggal" class="form-control"
                   value="{{ date('Y-m-d') }}" required>
        </div>

        <button class="btn btn-danger">Simpan</button>
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

{{-- SCRIPT --}}
<script>
    const stockPerExp = @json($stock_per_exp);

    document.getElementById('product_id').addEventListener('change', function () {
        const expSelect = document.getElementById('tanggal_kadaluarsa');
        expSelect.innerHTML = '<option value="">-- Pilih Tanggal Kadaluarsa --</option>';

        if (stockPerExp[this.value]) {

            // 🔥 SORT TANGGAL KADALUARSA TERDEKAT
            const sorted = stockPerExp[this.value].sort((a, b) => {
                if (!a.tanggal_kadaluarsa) return 1;
                if (!b.tanggal_kadaluarsa) return -1;
                return new Date(a.tanggal_kadaluarsa) - new Date(b.tanggal_kadaluarsa);
            });

            sorted.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.barang_masuk_id;
                opt.dataset.satuan = item.satuan;
                opt.text =
                    (item.tanggal_kadaluarsa ?? 'Tanpa Exp') +
                    ' (Stok: ' + item.total_stok + ' ' + item.satuan + ')';
                expSelect.appendChild(opt);
            });
        }
    });

    document.getElementById('tanggal_kadaluarsa').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        document.getElementById('barang_masuk_id').value = selected.value;
        document.getElementById('satuan').value = selected.dataset.satuan || '';
    });
</script>
@endsection

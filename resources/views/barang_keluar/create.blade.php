@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">📤 Tambah Barang Keluar</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('barang-keluar.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Barang</label>
            <select id="product_id" name="product_id" class="form-select" required>
                <option value="">-- Pilih Barang --</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_barang }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal Kadaluarsa (Batch)</label>
            <select id="barang_masuk_id" name="barang_masuk_id" class="form-select" required>
                <option value="">-- Pilih Barang Terlebih Dahulu --</option>
            </select>
        </div>

        {{-- HIDDEN SATUAN --}}
        <input type="hidden" name="satuan_id" id="satuan_id">

        <div class="mb-3">
            <label>Jumlah Keluar</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Keluar</label>
            <input type="date" name="tanggal" class="form-control"
                   value="{{ date('Y-m-d') }}" required>
        </div>

        <button class="btn btn-danger">Simpan</button>
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
const productSelect = document.getElementById('product_id');
const batchSelect   = document.getElementById('barang_masuk_id');
const satuanInput   = document.getElementById('satuan_id');

productSelect.addEventListener('change', function () {
    const productId = this.value;
    batchSelect.innerHTML = '<option>Loading...</option>';

    if (!productId) {
        batchSelect.innerHTML = '<option>-- Pilih Produk --</option>';
        return;
    }

    fetch(`/barang-keluar/batch/${productId}`)
        .then(res => res.json())
        .then(data => {
            batchSelect.innerHTML = '<option value="">-- Pilih Kadaluarsa --</option>';

            if (data.length === 0) {
                batchSelect.innerHTML = '<option>Stok kosong</option>';
                return;
            }

            data.forEach(item => {
                const opt = document.createElement('option');
                // ambil ID pertama sebagai value (atau bisa array jika multiple)
                opt.value = item.ids[0];
                opt.dataset.satuan = item.satuan_id;
                opt.textContent = `${item.tanggal_kadaluarsa} | Stok: ${item.stok} ${item.satuan}`;
                batchSelect.appendChild(opt);
            });
        });
});

batchSelect.addEventListener('change', function () {
    const selected = this.selectedOptions[0];
    satuanInput.value = selected.dataset.satuan || '';
});
</script>
@endsection

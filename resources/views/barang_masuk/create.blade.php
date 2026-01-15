@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Tambah Barang Masuk</h4>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form action="{{ route('barang-masuk.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Barang</label>
            <select id="product_id" name="product_id" class="form-select" required>
                <option value="">-- Pilih Barang --</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" data-satuans='@json($p->satuans)'>
                        {{ $p->nama_barang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah Masuk</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label>Satuan</label>
            <select id="satuan_id" name="satuan_id" class="form-select" required>
                <option value="">-- Pilih Satuan --</option>
            </select>

        </div>

        <div class="mb-3">
            <label>Tanggal Kadaluarsa</label>
            <input type="date" name="tanggal_kadaluarsa" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tanggal Masuk</label>
            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
    <script>
        document.getElementById('product_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const satuans = selectedOption.dataset.satuans ?
                JSON.parse(selectedOption.dataset.satuans) :
                [];

            const satuanSelect = document.getElementById('satuan_id');
            satuanSelect.innerHTML = '<option value="">-- Pilih Satuan --</option>';

            satuans.forEach(satuan => {
                const option = document.createElement('option');
                option.value = satuan.id;
                option.textContent = satuan.nama;
                satuanSelect.appendChild(option);
            });
        });
    </script>
@endsection

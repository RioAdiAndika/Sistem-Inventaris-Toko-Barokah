@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Tambah Barang</h4>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kode Barang</label>
            <input type="text" name="kode_barang"
                class="form-control @error('kode_barang') is-invalid @enderror"
                value="{{ old('kode_barang') }}">

            @error('kode_barang')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" name="nama_barang"
                class="form-control @error('nama_barang') is-invalid @enderror"
                value="{{ old('nama_barang') }}">

            @error('nama_barang')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori"
                class="form-select @error('kategori') is-invalid @enderror">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriOptions as $k)
                    <option value="{{ $k }}" {{ old('kategori') == $k ? 'selected' : '' }}>
                        {{ $k }}
                    </option>
                @endforeach
            </select>

            @error('kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Satuan Barang</label>
            <div class="row">
                @foreach ($satuans as $satuan)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                name="satuan_ids[]"
                                value="{{ $satuan->id }}"
                                {{ in_array($satuan->id, old('satuan_ids', [])) ? 'checked' : '' }}>

                            <label class="form-check-label">
                                {{ $satuan->nama }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            @error('satuan_ids')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Stok Minimal</label>
            <input type="number" name="stok_minimal"
                class="form-control @error('stok_minimal') is-invalid @enderror"
                value="{{ old('stok_minimal', 0) }}">

            @error('stok_minimal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Satuan Stok Minimal</label>

            <select name="stok_minimal_satuan_id"
                id="stok_minimal_satuan_id"
                class="form-select @error('stok_minimal_satuan_id') is-invalid @enderror"
                disabled>

                <option value="">-- Pilih Satuan Minimal --</option>

                @foreach ($satuans as $satuan)
                    <option value="{{ $satuan->id }}">
                        {{ $satuan->nama }}
                    </option>
                @endforeach
            </select>

            @error('stok_minimal_satuan_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar Produk</label>
            <input type="file" name="gambar"
                class="form-control @error('gambar') is-invalid @enderror">

            @error('gambar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary">
            💾 Simpan
        </button>
    </form>
    <script>
    const checkboxes = document.querySelectorAll('input[name="satuan_ids[]"]');
    const satuanSelect = document.getElementById('stok_minimal_satuan_id');

    function updateSatuanMinimal() {
        const checked = [...checkboxes].filter(cb => cb.checked);

        satuanSelect.innerHTML = '<option value="">-- Pilih Satuan Minimal --</option>';

        if (checked.length === 0) {
            satuanSelect.disabled = true;
            return;
        }

        satuanSelect.disabled = false;

        checked.forEach(cb => {
            const label = cb.closest('.form-check').querySelector('label').innerText;
            const opt = document.createElement('option');
            opt.value = cb.value;
            opt.textContent = label;
            satuanSelect.appendChild(opt);
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateSatuanMinimal));
</script>

@endsection

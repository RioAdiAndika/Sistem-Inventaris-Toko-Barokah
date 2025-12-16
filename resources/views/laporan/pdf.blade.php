<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inventaris</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h4>Laporan Inventaris Produk</h4>
    <p>Tanggal: {{ date('d-m-Y H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Stok Minimal</th>
                <th>Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->kode_barang }}</td>
                <td>{{ $p->nama_barang }}</td>
                <td>{{ $p->kategori }}</td>
                <td>{{ $p->stok }}</td>
                <td>{{ $p->stok_minimal }}</td>
                <td>{{ $p->created_at->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

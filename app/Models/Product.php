<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'stok',
        'stok_minimal',
        'stok_minimal_satuan_id',
        'gambar'
    ];
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }


    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class);
    }
    public function satuans()
{
    return $this->belongsToMany(Satuan::class, 'product_satuan');

}
public function stokMinimalSatuan()
{
    return $this->belongsTo(Satuan::class, 'stok_minimal_satuan_id');
}

    // ✅ stok otomatis
    public function getStokAttribute()
    {
        $masuk = $this->barangMasuk()->sum('jumlah');
        $keluar = $this->barangKeluar()->sum('jumlah');
        return $masuk - $keluar;
    }
}

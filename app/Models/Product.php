<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'gambar',
        'kategori',
        'stok',
        'stok_minimal'

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

    // ✅ stok otomatis
    public function getStokAttribute()
    {
        $masuk = $this->barangMasuk()->sum('jumlah');
        $keluar = $this->barangKeluar()->sum('jumlah');
        return $masuk - $keluar;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = [
        'product_id',
        'barang_masuk_id',
        'jumlah',
        'satuan',
        'tanggal',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class);
    }
}

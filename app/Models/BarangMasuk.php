<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'product_id',
        'jumlah',
        'stok',
        'tanggal',
        'tanggal_kadaluarsa',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

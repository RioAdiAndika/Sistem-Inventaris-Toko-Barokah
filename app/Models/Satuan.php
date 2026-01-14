<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $fillable = ['nama'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_satuan');
    }
}


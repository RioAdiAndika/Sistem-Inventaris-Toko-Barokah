<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
       Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('kode_barang')->unique();
    $table->string('nama_barang');
    $table->string('kategori');
    $table->integer('stok')->default(0);
    $table->integer('stok_minimal')->default(5);
    $table->timestamps();
});

    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};

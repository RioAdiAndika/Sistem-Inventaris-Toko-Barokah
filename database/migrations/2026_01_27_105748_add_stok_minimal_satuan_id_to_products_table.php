<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->foreignId('stok_minimal_satuan_id')
            ->nullable()
            ->after('stok_minimal')
            ->constrained('satuans')
            ->nullOnDelete();
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropForeign(['stok_minimal_satuan_id']);
        $table->dropColumn('stok_minimal_satuan_id');
    });
}

};

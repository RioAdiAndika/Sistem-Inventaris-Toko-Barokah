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
    Schema::table('product_satuan', function (Blueprint $table) {
        $table->integer('stok_minimal')->default(0)->after('satuan_id');
    });
}

public function down()
{
    Schema::table('product_satuan', function (Blueprint $table) {
        $table->dropColumn('stok_minimal');
    });
}

};

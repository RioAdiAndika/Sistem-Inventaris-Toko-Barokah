<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('satuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // dus, bal, slop, pcs, dll
            $table->timestamps();
        });

        // 🔥 INSERT DATA DEFAULT SATUAN
        DB::table('satuans')->insert([
            ['nama' => 'Bal',   ],
            ['nama' => 'Dus',   ],
            ['nama' => 'Karung',],
            ['nama' => 'Pack',  ],
            ['nama' => 'Pcs',   ],
            ['nama' => 'Slop',  ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satuans');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['artificial', 'fresh', 'pendukung']);
            $table->string('satuan');
            $table->decimal('stok', 10, 2)->default(0);
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->decimal('harga_beli_terakhir', 12, 2)->default(0);
            $table->boolean('dijual_eceran')->default(false);
            $table->decimal('harga_eceran', 12, 2)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan');
    }
};

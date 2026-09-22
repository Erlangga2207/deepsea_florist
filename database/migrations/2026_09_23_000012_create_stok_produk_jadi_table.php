<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_produk_jadi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->nullable()->constrained('produk');
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanan');
            $table->string('nama');
            $table->string('foto')->nullable();
            $table->decimal('harga_jual', 12, 2)->nullable();
            $table->enum('asal', ['batal', 'produksi']);
            $table->enum('status', ['tersedia', 'terjual'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_produk_jadi');
    }
};

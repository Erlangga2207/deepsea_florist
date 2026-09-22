<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori');
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('meta_deskripsi', 160)->nullable();
            $table->string('foto_utama')->nullable();
            $table->decimal('harga_dasar', 12, 2)->nullable();
            $table->boolean('tampilkan_harga')->default(false);
            $table->decimal('estimasi_jam', 5, 2)->default(0);
            $table->tinyInteger('faktor_kerumitan')->default(1);
            $table->enum('status', ['ready', 'preorder'])->default('ready');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};

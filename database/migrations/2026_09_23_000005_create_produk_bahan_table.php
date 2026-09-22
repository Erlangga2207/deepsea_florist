<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('bahan_id')->constrained('bahan');
            $table->decimal('jumlah', 10, 2);
            $table->timestamps();

            $table->unique(['produk_id', 'bahan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_bahan');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan');
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanan');
            $table->foreignId('pengeluaran_id')->nullable()->constrained('pengeluaran');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('tipe', ['masuk', 'keluar', 'penyesuaian']);
            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok');
    }
};

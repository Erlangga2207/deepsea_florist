<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggan');
            $table->foreignId('user_id')->constrained('users');
            $table->date('tanggal_pesan');
            $table->date('tanggal_jadi')->index();
            $table->enum('metode_ambil', ['ambil', 'antar'])->default('ambil');
            $table->text('alamat_antar')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('biaya_tambahan', 12, 2)->default(0);
            $table->decimal('ongkir', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('total_dibayar', 12, 2)->default(0);
            $table->enum('status', ['masuk', 'dikerjakan', 'jadi', 'selesai', 'batal'])->default('masuk')->index();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};

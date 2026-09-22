<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko');
            $table->text('alamat')->nullable();
            $table->string('jam_buka')->nullable();
            $table->string('no_wa')->nullable();
            $table->string('link_ig')->nullable();
            $table->string('link_tiktok')->nullable();
            $table->text('tentang')->nullable();
            $table->tinyInteger('jumlah_perakit')->default(3);
            $table->decimal('jam_kerja_per_hari', 4, 2)->default(6);
            $table->decimal('margin_default', 5, 2)->default(1.60);
            $table->decimal('tarif_jasa_per_jam', 12, 2)->default(10000);

            // SEO, dari docs/08-SEO.md bagian A1
            $table->string('meta_judul_default')->nullable();
            $table->text('meta_deskripsi_default')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};

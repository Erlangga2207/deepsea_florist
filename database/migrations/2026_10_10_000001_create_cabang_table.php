<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Alamat & koordinat pindah dari pengaturan ke cabang utama, supaya hanya ada satu sumber data alamat.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabang', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->text('alamat');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('url_maps')->nullable();
            $table->string('no_wa', 20)->nullable();
            $table->string('jam_buka', 150)->nullable();
            $table->boolean('is_utama')->default(false);
            $table->boolean('tampil_di_website')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['tampil_di_website', 'urutan']);
        });

        $pengaturan = DB::table('pengaturan')->find(1);
        if ($pengaturan && $pengaturan->alamat) {
            DB::table('cabang')->insert([
                'nama' => 'Cabang utama',
                'alamat' => $pengaturan->alamat,
                // Kalau koordinat belum pernah diisi, pakai titik perkiraan Cibogo; owner mengoreksi lewat menu Cabang
                'latitude' => $pengaturan->latitude ?? -6.5236,
                'longitude' => $pengaturan->longitude ?? 107.8020,
                'url_maps' => $pengaturan->google_maps_url,
                'jam_buka' => $pengaturan->jam_buka,
                'is_utama' => true,
                'tampil_di_website' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'latitude', 'longitude', 'google_maps_url']);
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();
        });

        $utama = DB::table('cabang')->where('is_utama', true)->first();
        if ($utama) {
            DB::table('pengaturan')->where('id', 1)->update([
                'alamat' => $utama->alamat,
                'latitude' => $utama->latitude,
                'longitude' => $utama->longitude,
                'google_maps_url' => $utama->url_maps,
            ]);
        }

        Schema::dropIfExists('cabang');
    }
};

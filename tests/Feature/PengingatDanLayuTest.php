<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengingatDanLayuTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function karyawan(): User
    {
        return User::where('role', 'karyawan')->first();
    }

    public function test_dashboard_menampilkan_pesanan_aktif_untuk_besok(): void
    {
        $besok = Pesanan::aktif()->first();
        $besok->update(['tanggal_jadi' => today()->addDay()]);
        $batal = Pesanan::where('status', 'batal')->first();
        $batal->update(['tanggal_jadi' => today()->addDay()]);

        $this->actingAs($this->karyawan())->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Diambil / diantar besok')
            ->assertSee($besok->kode)
            ->assertDontSee($batal->kode);

    }

    public function test_stok_masuk_fresh_bisa_membuang_sisa_layu(): void
    {
        $bahan = Bahan::where('jenis', 'fresh')->first();
        $bahan->forceFill(['stok' => 30])->save();

        $this->actingAs($this->karyawan())->post(route('admin.mutasi-stok.store'), [
            'bahan_id' => $bahan->id,
            'tipe' => 'masuk',
            'jumlah' => 50,
            'tanggal' => today()->toDateString(),
            'buang_layu' => 1,
            'jumlah_layu' => 20,
        ])->assertRedirect();

        // 30 − 20 layu + 50 baru
        $this->assertEquals(60, $bahan->fresh()->stok);
        $this->assertDatabaseHas('mutasi_stok', ['bahan_id' => $bahan->id, 'tipe' => 'keluar', 'jumlah' => 20, 'keterangan' => 'Bunga layu, dibuang saat stok baru masuk']);
        $this->assertDatabaseHas('mutasi_stok', ['bahan_id' => $bahan->id, 'tipe' => 'masuk', 'jumlah' => 50]);
    }

    public function test_form_stok_masuk_menampilkan_opsi_layu(): void
    {
        $this->actingAs($this->karyawan())->get(route('admin.mutasi-stok.create'))
            ->assertOk()->assertSee('Buang sisa stok lama yang sudah layu');
    }

    public function test_tanpa_centang_stok_lama_tidak_dibuang(): void
    {
        $bahan = Bahan::where('jenis', 'fresh')->first();
        $bahan->forceFill(['stok' => 30])->save();

        $this->actingAs($this->karyawan())->post(route('admin.mutasi-stok.store'), [
            'bahan_id' => $bahan->id, 'tipe' => 'masuk', 'jumlah' => 50, 'tanggal' => today()->toDateString(),
            'buang_layu' => 0, 'jumlah_layu' => 30,
        ])->assertRedirect();

        $this->assertEquals(80, $bahan->fresh()->stok);
    }

    public function test_bahan_artificial_mengabaikan_buang_layu(): void
    {
        $bahan = Bahan::where('jenis', 'artificial')->first();
        $stokAwal = (float) $bahan->stok;

        $this->actingAs($this->karyawan())->post(route('admin.mutasi-stok.store'), [
            'bahan_id' => $bahan->id, 'tipe' => 'masuk', 'jumlah' => 5, 'tanggal' => today()->toDateString(),
            'buang_layu' => 1, 'jumlah_layu' => 3,
        ])->assertRedirect();

        $this->assertEquals($stokAwal + 5, $bahan->fresh()->stok);
    }

    public function test_centang_tanpa_jumlah_ditolak(): void
    {
        $bahan = Bahan::where('jenis', 'fresh')->first();

        $this->actingAs($this->karyawan())->post(route('admin.mutasi-stok.store'), [
            'bahan_id' => $bahan->id, 'tipe' => 'masuk', 'jumlah' => 5, 'tanggal' => today()->toDateString(),
            'buang_layu' => 1,
        ])->assertSessionHasErrors('jumlah_layu');
    }
}

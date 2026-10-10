<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CabangTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function owner(): User
    {
        return User::where('role', 'owner')->first();
    }

    private function isian(array $ubah = []): array
    {
        return $ubah + [
            'nama' => 'Cabang Pagaden',
            'alamat' => 'Jl. Raya Pagaden, Subang',
            'koordinat' => '-6.4512, 107.7801',
            'tampil_di_website' => '1',
        ];
    }

    public function test_karyawan_ditolak_mengelola_cabang(): void
    {
        $karyawan = User::where('role', 'karyawan')->first();
        $utama = Cabang::utama();

        $this->actingAs($karyawan)->get(route('admin.cabang.index'))->assertForbidden();
        $this->actingAs($karyawan)->post(route('admin.cabang.store'), $this->isian())->assertForbidden();
        $this->actingAs($karyawan)->put(route('admin.cabang.update', $utama), $this->isian())->assertForbidden();
        $this->actingAs($karyawan)->delete(route('admin.cabang.destroy', $utama))->assertForbidden();
    }

    public function test_cabang_utama_tidak_bisa_dihapus(): void
    {
        $utama = Cabang::utama();

        $this->actingAs($this->owner())->delete(route('admin.cabang.destroy', $utama))->assertSessionHas('gagal');
        $this->assertModelExists($utama);
    }

    public function test_menjadikan_utama_melepas_yang_lama(): void
    {
        $lama = Cabang::utama();

        $this->actingAs($this->owner())->post(route('admin.cabang.store'), $this->isian(['is_utama' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertFalse($lama->fresh()->is_utama);
        $this->assertSame(1, Cabang::where('is_utama', true)->count());
        $this->assertSame('Cabang Pagaden', Cabang::utama()->nama);
    }

    public function test_cabang_utama_tidak_bisa_dilepas_atau_disembunyikan(): void
    {
        $utama = Cabang::utama();
        $isian = $this->isian(['nama' => $utama->nama, 'koordinat' => $utama->koordinat()]);

        $this->actingAs($this->owner())->put(route('admin.cabang.update', $utama), $isian)
            ->assertSessionHasErrors('is_utama');
        $this->actingAs($this->owner())->put(route('admin.cabang.update', $utama), ['is_utama' => '1', 'tampil_di_website' => '0'] + $isian)
            ->assertSessionHasErrors('tampil_di_website');
    }

    public function test_koordinat_valid_diurai_jadi_latitude_longitude(): void
    {
        $this->actingAs($this->owner())->post(route('admin.cabang.store'), $this->isian(['koordinat' => ' -6.5312 ,107.7234 ']))
            ->assertSessionHasNoErrors();

        $cabang = Cabang::where('nama', 'Cabang Pagaden')->first();
        $this->assertEquals(-6.5312, (float) $cabang->latitude);
        $this->assertEquals(107.7234, (float) $cabang->longitude);
    }

    public function test_koordinat_salah_ditolak(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('admin.cabang.store'), $this->isian(['koordinat' => 'Cibogo, Subang']))
            ->assertSessionHasErrors('koordinat');
        $this->actingAs($owner)->post(route('admin.cabang.store'), $this->isian(['koordinat' => '-6.5312']))
            ->assertSessionHasErrors('koordinat');
        // lat/lng tertukar
        $this->actingAs($owner)->post(route('admin.cabang.store'), $this->isian(['koordinat' => '107.7234, -6.5312']))
            ->assertSessionHasErrors('latitude');
        $this->actingAs($owner)->post(route('admin.cabang.store'), $this->isian(['url_maps' => 'https://contoh.com/maps']))
            ->assertSessionHasErrors('url_maps');

        $this->assertDatabaseMissing('cabang', ['nama' => 'Cabang Pagaden']);
    }

    public function test_no_wa_dinormalisasi_ke_62(): void
    {
        foreach (['0812-9683-6363', '+62 812 9683 6363', '6281296836363'] as $nomor) {
            $this->assertSame('6281296836363', (new Cabang(['no_wa' => $nomor]))->no_wa);
        }
        $this->assertNull((new Cabang(['no_wa' => '']))->no_wa);
    }

    public function test_halaman_kontak_menampilkan_cabang_yang_tampil(): void
    {
        Cabang::create(['nama' => 'Gudang rahasia', 'alamat' => 'Tidak untuk publik', 'latitude' => -6.45, 'longitude' => 107.78, 'tampil_di_website' => false]);

        $respons = $this->get(route('publik.kontak'))->assertOk();

        $this->assertSame(2, Cabang::tampil()->count());
        Cabang::tampil()->get()->each(fn ($c) => $respons->assertSee($c->nama)->assertSee($c->arah_url));
        $respons->assertDontSee('Gudang rahasia');
    }
}

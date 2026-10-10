<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotaPesananTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function karyawan(): User
    {
        return User::where('role', 'karyawan')->first();
    }

    public function test_pesanan_dengan_pembayaran_tampil_inline_sebagai_pdf(): void
    {
        $pesanan = Pesanan::where('kode', 'DF-2609-041')->first();

        $this->actingAs($this->karyawan())->get(route('admin.pesanan.show', $pesanan))->assertSee('Cetak nota');

        $this->actingAs($this->karyawan())->get(route('admin.pesanan.nota', $pesanan))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename=nota-DF-2609-041.pdf');
    }

    public function test_unduh_memberi_header_attachment_dengan_nama_benar(): void
    {
        $pesanan = Pesanan::where('kode', 'DF-2609-042')->first();

        $this->actingAs($this->karyawan())->get(route('admin.pesanan.nota', $pesanan).'?unduh=1')
            ->assertOk()
            ->assertDownload('nota-DF-2609-042.pdf');
    }

    public function test_pesanan_tanpa_pembayaran_404(): void
    {
        $pesanan = Pesanan::where('kode', 'DF-2609-046')->first();
        $pesanan->pembayaran()->delete();

        $this->actingAs($this->karyawan())->get(route('admin.pesanan.show', $pesanan))->assertDontSee('Cetak nota');

        $this->actingAs($this->karyawan())->get(route('admin.pesanan.nota', $pesanan))->assertNotFound();
    }

    public function test_pesanan_batal_404(): void
    {
        $pesanan = Pesanan::where('status', 'batal')->first();
        $this->assertTrue($pesanan->pembayaran()->exists());

        $this->actingAs($this->karyawan())->get(route('admin.pesanan.nota', $pesanan))->assertNotFound();
    }

    public function test_catatan_panjang_tetap_satu_halaman(): void
    {
        $pesanan = Pesanan::where('kode', 'DF-2609-043')->first();
        $pesanan->update(['catatan' => str_repeat('Bunga mawar merah dan putih disusun melingkar, pita krem, kertas cokelat. ', 15)]);

        $pdf = $this->actingAs($this->karyawan())->get(route('admin.pesanan.nota', $pesanan))->getContent();

        $this->assertSame(1, preg_match_all('#/Type\s*/Page[^s]#', $pdf));
    }
}

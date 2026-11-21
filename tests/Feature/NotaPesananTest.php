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

    public function test_tiap_ukuran_satu_halaman_dengan_lebar_kertas_benar(): void
    {
        $pesanan = Pesanan::where('kode', 'DF-2609-041')->first();
        // Lebar kertas dalam pt: A4 595, A5 420, 80 mm 227, 58 mm 164.
        $lebar = ['setengah-a4' => 595, 'a5' => 420, 'struk-80' => 227, 'struk-58' => 164];

        foreach ($lebar as $ukuran => $pt) {
            $pdf = $this->actingAs($this->karyawan())
                ->get(route('admin.pesanan.nota', [$pesanan, 'ukuran' => $ukuran]))
                ->assertOk()->getContent();

            $this->assertSame(1, preg_match_all('#/Type\s*/Page[^s]#', $pdf), $ukuran);
            preg_match('#/MediaBox\s*\[\s*[\d.]+\s+[\d.]+\s+([\d.]+)\s+([\d.]+)#', $pdf, $box);
            $this->assertEqualsWithDelta($pt, (float) $box[1], 1, $ukuran);

            if (str_starts_with($ukuran, 'struk-')) {
                $this->assertLessThan(1000, (float) $box[2], "$ukuran: tinggi mengikuti isi");
            }
        }
    }

    public function test_struk_memanjang_mengikuti_catatan(): void
    {
        $pesanan = Pesanan::where('kode', 'DF-2609-043')->first();
        $tinggi = function () use ($pesanan) {
            $pdf = $this->actingAs($this->karyawan())
                ->get(route('admin.pesanan.nota', [$pesanan, 'ukuran' => 'struk-58']))->getContent();
            preg_match('#/MediaBox\s*\[\s*[\d.]+\s+[\d.]+\s+[\d.]+\s+([\d.]+)#', $pdf, $box);
            $this->assertSame(1, preg_match_all('#/Type\s*/Page[^s]#', $pdf));

            return (float) $box[1];
        };

        $pendek = $tinggi();
        $pesanan->update(['catatan' => str_repeat('Mawar merah dan putih, pita krem. ', 20)]);

        $this->assertGreaterThan($pendek + 50, $tinggi());
    }

    public function test_ukuran_tidak_dikenal_404(): void
    {
        $pesanan = Pesanan::where('kode', 'DF-2609-041')->first();

        $this->actingAs($this->karyawan())
            ->get(route('admin.pesanan.nota', [$pesanan, 'ukuran' => 'f4']))
            ->assertNotFound();
    }

    public function test_catatan_panjang_tetap_satu_halaman(): void
    {
        $pesanan = Pesanan::where('kode', 'DF-2609-043')->first();
        $pesanan->update(['catatan' => str_repeat('Bunga mawar merah dan putih disusun melingkar, pita krem, kertas cokelat. ', 15)]);

        $pdf = $this->actingAs($this->karyawan())->get(route('admin.pesanan.nota', $pesanan))->getContent();

        $this->assertSame(1, preg_match_all('#/Type\s*/Page[^s]#', $pdf));
    }
}

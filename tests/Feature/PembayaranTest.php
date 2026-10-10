<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembayaranTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'owner']);
    }

    private function pesanan(array $data = []): Pesanan
    {
        return Pesanan::create($data + [
            'kode' => 'DS-TES-'.uniqid(),
            'pelanggan_id' => Pelanggan::create(['nama' => 'Tes'])->id,
            'user_id' => $this->user->id,
            'tanggal_pesan' => today(),
            'tanggal_jadi' => today()->addDay(),
            'total' => 390000,
        ]);
    }

    private function bayar(Pesanan $pesanan, $jumlah)
    {
        return $this->actingAs($this->user)
            ->post(route('admin.pesanan.pembayaran', $pesanan), ['jumlah' => $jumlah, 'metode' => 'cash', 'tanggal' => today()->toDateString()]);
    }

    public function test_bayar_tepat_sisa_tagihan_berhasil(): void
    {
        $pesanan = $this->pesanan();
        $this->bayar($pesanan, 195000)->assertSessionHasNoErrors();
        $this->bayar($pesanan, 195000)->assertSessionHasNoErrors();

        $this->assertEquals(0, $pesanan->fresh()->sisaTagihan());
    }

    public function test_bayar_melebihi_sisa_ditolak(): void
    {
        $pesanan = $this->pesanan();
        $this->bayar($pesanan, 390001)->assertSessionHasErrors('jumlah');

        $this->assertSame(0, $pesanan->pembayaran()->count());
    }

    public function test_pembayaran_ketiga_ditolak(): void
    {
        // Dua pembayaran dibuat langsung agar sisa tetap ada; cek khusus batas dua kali.
        $pesanan = $this->pesanan();
        $pesanan->pembayaran()->createMany([
            ['jenis' => 'dp', 'jumlah' => 100000, 'metode' => 'cash', 'tanggal' => today()],
            ['jenis' => 'dp', 'jumlah' => 100000, 'metode' => 'cash', 'tanggal' => today()],
        ]);
        $pesanan->update(['total_dibayar' => 200000]);

        $this->bayar($pesanan, 1000)->assertSessionHasErrors('jumlah');
        $this->assertSame(2, $pesanan->pembayaran()->count());
    }

    public function test_bayar_pesanan_batal_ditolak(): void
    {
        $pesanan = $this->pesanan(['status' => 'batal']);
        $this->bayar($pesanan, 1000)->assertSessionHasErrors('jumlah');

        $this->assertSame(0, $pesanan->pembayaran()->count());
    }

    public function test_selesai_diblokir_kalau_belum_lunas(): void
    {
        $pesanan = $this->pesanan(['status' => 'jadi', 'total_dibayar' => 195000]);
        $this->actingAs($this->user)->post(route('admin.pesanan.status', $pesanan), ['status' => 'selesai'])
            ->assertSessionHas('gagal');

        $this->assertSame('jadi', $pesanan->fresh()->status);
    }
}

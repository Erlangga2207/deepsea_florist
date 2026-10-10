<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private const KHUSUS_OWNER = ['admin.laporan.index', 'admin.pengeluaran.index', 'admin.pengguna.index', 'admin.pengaturan.edit'];

    public function test_karyawan_tidak_melihat_menu_khusus_owner(): void
    {
        $html = $this->actingAs(User::where('role', 'karyawan')->first())
            ->get(route('admin.dashboard'))->assertOk()->getContent();

        foreach (self::KHUSUS_OWNER as $rute) {
            $this->assertStringNotContainsString('href="'.route($rute).'"', $html, $rute);
        }
        $this->assertStringNotContainsString('Keuangan', $html);
    }

    public function test_owner_melihat_menu_khusus_owner(): void
    {
        $html = $this->actingAs(User::where('role', 'owner')->first())
            ->get(route('admin.dashboard'))->assertOk()->getContent();

        foreach (self::KHUSUS_OWNER as $rute) {
            $this->assertStringContainsString('href="'.route($rute).'"', $html, $rute);
        }
    }
}

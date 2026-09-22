<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PenggunaRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Tidak ada hapus akun: pesanan & mutasi merujuk ke user. Cukup nonaktifkan.
class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = User::orderByDesc('is_aktif')->orderBy('role')->orderBy('name')->get();

        return view('admin.pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        return view('admin.pengguna.form', ['pengguna' => new User(['role' => 'karyawan', 'is_aktif' => true])]);
    }

    public function store(PenggunaRequest $request)
    {
        User::create($request->validated());

        return redirect()->route('admin.pengguna.index')->with('sukses', 'Akun dibuat. Berikan email dan kata sandinya ke yang bersangkutan.');
    }

    public function edit(User $pengguna)
    {
        return view('admin.pengguna.form', compact('pengguna'));
    }

    public function update(PenggunaRequest $request, User $pengguna)
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $pengguna->update($data);

        // Akun nonaktif atau sandinya diganti: putus semua sesi & "ingat saya" yang masih terbuka
        if (! $pengguna->is_aktif || isset($data['password'])) {
            DB::table('sessions')->where('user_id', $pengguna->id)->where('id', '!=', session()->getId())->delete();
            $pengguna->forceFill(['remember_token' => null])->save();
        }

        return redirect()->route('admin.pengguna.index')->with('sukses', "Akun {$pengguna->name} diperbarui.");
    }
}

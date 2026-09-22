<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengaturanRequest;
use App\Models\Pengaturan;

class PengaturanController extends Controller
{
    public function edit()
    {
        return view('admin.pengaturan.edit', ['pengaturan' => Pengaturan::ambil()]);
    }

    public function update(PengaturanRequest $request)
    {
        Pengaturan::ambil()->update($request->validated());

        return redirect()->route('admin.pengaturan.edit')->with('sukses', 'Pengaturan disimpan.');
    }
}

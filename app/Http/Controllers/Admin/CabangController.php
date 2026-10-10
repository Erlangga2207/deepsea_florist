<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CabangRequest;
use App\Models\Cabang;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
    public function index()
    {
        return view('admin.cabang.index', ['cabang' => Cabang::urut()->get()]);
    }

    public function create()
    {
        return view('admin.cabang.form', ['cabang' => new Cabang(['tampil_di_website' => true])]);
    }

    public function store(CabangRequest $request)
    {
        $this->simpan(new Cabang, $request);

        return redirect()->route('admin.cabang.index')->with('sukses', 'Cabang ditambahkan.');
    }

    public function edit(Cabang $cabang)
    {
        return view('admin.cabang.form', compact('cabang'));
    }

    public function update(CabangRequest $request, Cabang $cabang)
    {
        $this->simpan($cabang, $request);

        return redirect()->route('admin.cabang.index')->with('sukses', 'Cabang diperbarui.');
    }

    public function destroy(Cabang $cabang)
    {
        if ($cabang->is_utama) {
            return back()->with('gagal', 'Cabang utama tidak bisa dihapus. Jadikan cabang lain utama dulu.');
        }

        $cabang->delete();

        return redirect()->route('admin.cabang.index')->with('sukses', 'Cabang dihapus.');
    }

    // Selalu tepat satu cabang utama: menjadikan cabang ini utama melepas yang lama.
    private function simpan(Cabang $cabang, CabangRequest $request): void
    {
        $data = $request->safe()->except('koordinat');
        $data['urutan'] ??= 0;

        DB::transaction(function () use ($cabang, $data) {
            if (! Cabang::where('is_utama', true)->whereKeyNot($cabang->id)->exists()) {
                $data['is_utama'] = true;
                $data['tampil_di_website'] = true;
            }
            if ($data['is_utama']) {
                Cabang::where('is_utama', true)->whereKeyNot($cabang->id)->update(['is_utama' => false]);
            }

            $cabang->fill($data)->save();
        });
    }
}

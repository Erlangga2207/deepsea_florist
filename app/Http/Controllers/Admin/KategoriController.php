<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public const TIPE_HARGA = [
        'artificial' => 'Artificial',
        'fresh' => 'Fresh (+10% cadangan layu)',
        'uang' => 'Buket uang',
        'papan' => 'Bunga papan',
        'umum' => 'Umum',
    ];

    public function index()
    {
        $kategori = Kategori::withCount('produk')->orderBy('urutan')->orderBy('nama')->get();

        return view('admin.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('admin.kategori.form', ['kategori' => new Kategori, 'tipeHarga' => self::TIPE_HARGA]);
    }

    public function store(KategoriRequest $request)
    {
        Kategori::create($this->data($request));

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.form', ['kategori' => $kategori, 'tipeHarga' => self::TIPE_HARGA]);
    }

    public function update(KategoriRequest $request, Kategori $kategori)
    {
        $kategori->update($this->data($request));

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->produk()->withTrashed()->exists()) {
            return back()->with('gagal', "Kategori {$kategori->nama} masih dipakai produk, jadi tidak bisa dihapus.");
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori dihapus.');
    }

    private function data(KategoriRequest $request): array
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['nama']);
        $data['urutan'] ??= 0;

        return $data;
    }
}

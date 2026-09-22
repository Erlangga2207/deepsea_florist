<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Harga\HargaFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\KomposisiRequest;
use App\Http\Requests\ProdukRequest;
use App\Models\Bahan;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\ProdukFoto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Support\FotoWebp;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::with('kategori')
            ->withCount('komposisi')
            ->when($request->kategori, fn ($q, $id) => $q->where('kategori_id', $id))
            ->when($request->cari, fn ($q, $cari) => $q->where(fn ($q) => $q->where('nama', 'like', "%{$cari}%")->orWhere('kode', 'like', "%{$cari}%")))
            ->orderBy('kode')
            ->paginate(20)
            ->withQueryString();

        return view('admin.produk.index', [
            'produk' => $produk,
            'kategori' => Kategori::orderBy('urutan')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.produk.form', [
            'produk' => new Produk(['faktor_kerumitan' => 1, 'status' => 'ready', 'is_aktif' => true]),
            'kategori' => Kategori::orderBy('urutan')->get(),
        ]);
    }

    public function store(ProdukRequest $request)
    {
        $produk = Produk::create($request->safe()->except(['foto_utama', 'galeri']));
        $this->simpanFoto($request, $produk);

        return redirect()->route('admin.produk.edit', $produk)
            ->with('sukses', "Produk {$produk->kode} ditambahkan. Sekarang isi komposisi bahannya di bawah.");
    }

    public function show(Request $request, Produk $produk)
    {
        $produk->load('kategori', 'komposisi.bahan');

        // Buket uang & bunga papan butuh angka tambahan; di sini diisi lewat form kecil di halaman
        $opsi = $request->only(['jumlah_lembar', 'tarif_lipat', 'sewa_rangka', 'ongkos_pasang']);
        $kalkulator = HargaFactory::untuk($produk, $opsi);

        return view('admin.produk.show', [
            'produk' => $produk,
            'kalkulator' => $kalkulator,
            'rincian' => $kalkulator->rincian(),
            'opsi' => $opsi,
        ]);
    }

    public function edit(Produk $produk)
    {
        $produk->load('foto', 'komposisi.bahan');

        return view('admin.produk.form', [
            'produk' => $produk,
            'kategori' => Kategori::orderBy('urutan')->get(),
            'semuaBahan' => Bahan::where('is_aktif', true)->orderBy('jenis')->orderBy('nama')->get(),
        ]);
    }

    public function update(ProdukRequest $request, Produk $produk)
    {
        $produk->update($request->safe()->except(['foto_utama', 'galeri']));
        $this->simpanFoto($request, $produk);

        return redirect()->route('admin.produk.edit', $produk)->with('sukses', 'Produk diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('admin.produk.index')->with('sukses', "Produk {$produk->kode} dihapus dari katalog.");
    }

    public function simpanKomposisi(KomposisiRequest $request, Produk $produk)
    {
        $baris = collect($request->validated('komposisi', []))
            ->mapWithKeys(fn ($b) => [$b['bahan_id'] => ['jumlah' => $b['jumlah']]]);

        $produk->bahan()->sync($baris);

        return redirect()->route('admin.produk.edit', $produk)->with('sukses', 'Komposisi bahan disimpan.');
    }

    public function hapusFoto(Produk $produk, ProdukFoto $foto)
    {
        FotoWebp::hapus($foto->file);
        $foto->delete();

        return back()->with('sukses', 'Foto dihapus.');
    }

    private function simpanFoto(ProdukRequest $request, Produk $produk): void
    {
        if ($request->hasFile('foto_utama')) {
            if ($produk->foto_utama) {
                FotoWebp::hapus($produk->foto_utama);
            }
            $produk->update(['foto_utama' => $this->unggah($request->file('foto_utama'), $produk)]);
        }

        $urutan = (int) $produk->foto()->max('urutan');
        foreach ($request->file('galeri', []) as $file) {
            $produk->foto()->create(['file' => $this->unggah($file, $produk), 'urutan' => ++$urutan]);
        }
    }

    // Nama file dari slug supaya terbaca Google (docs/08-SEO.md), contoh buket-lily-pink-deepsea-florist-x7k2p.jpg
    private function unggah(UploadedFile $file, Produk $produk): string
    {
        $nama = $produk->slug.'-deepsea-florist-'.Str::lower(Str::random(5)).'.'.$file->extension();

        $path = $file->storeAs('produk', $nama, 'public');
        FotoWebp::buat($path);

        return $path;
    }
}

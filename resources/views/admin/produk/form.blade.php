@extends('layouts.admin')

@section('judul', $produk->exists ? $produk->nama : 'Tambah produk')

@section('konten')
    <div class="judul-halaman">
        <div>
            <h1>{{ $produk->exists ? $produk->nama : 'Tambah produk' }}</h1>
            @if ($produk->exists)
                <div class="text-ink-2">{{ $produk->kode }} · {{ $produk->kategori->nama }}</div>
            @endif
        </div>
        @if ($produk->exists)
            <a href="{{ route('admin.produk.show', $produk) }}" class="btn btn-garis ms-auto">Rincian harga</a>
            <form method="POST" action="{{ route('admin.produk.destroy', $produk) }}"
                  onsubmit="return confirm('Hapus {{ $produk->nama }} dari katalog? Riwayat pesanannya tetap aman.')">
                @csrf @method('DELETE')
                <button class="btn btn-polos">Hapus produk</button>
            </form>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <form method="POST" enctype="multipart/form-data"
                  action="{{ $produk->exists ? route('admin.produk.update', $produk) : route('admin.produk.store') }}">
                @csrf
                @if ($produk->exists) @method('PUT') @endif

                <x-panel judul="Data produk">
                    <div class="row">
                        <x-field class="col-sm-7" label="Nama model" name="nama" :value="$produk->nama" wajib />
                        <x-field class="col-sm-5" label="Kategori" name="kategori_id" wajib>
                            <select id="kategori_id" name="kategori_id" class="form-select" required>
                                <option value="">Pilih kategori</option>
                                @foreach ($kategori as $k)
                                    <option value="{{ $k->id }}" @selected(old('kategori_id', $produk->kategori_id) == $k->id)>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>

                    <x-field label="Deskripsi" name="deskripsi">
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="form-control">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                    </x-field>

                    <div class="row">
                        <x-field class="col-sm-4" label="Estimasi jam" name="estimasi_jam" type="number" step="0.25" min="0"
                                 :value="$produk->estimasi_jam" hint="Lama merakit 1 buket." />
                        <x-field class="col-sm-4" label="Kerumitan" name="faktor_kerumitan" wajib>
                            <select id="faktor_kerumitan" name="faktor_kerumitan" class="form-select">
                                @foreach ([1 => '1 · Sederhana', 2 => '2 · Sedang', 3 => '3 · Rumit'] as $nilai => $label)
                                    <option value="{{ $nilai }}" @selected(old('faktor_kerumitan', $produk->faktor_kerumitan) == $nilai)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field class="col-sm-4" label="Status" name="status" wajib>
                            <select id="status" name="status" class="form-select">
                                <option value="ready" @selected(old('status', $produk->status) === 'ready')>Ready</option>
                                <option value="preorder" @selected(old('status', $produk->status) === 'preorder')>Pre-order</option>
                            </select>
                        </x-field>
                    </div>

                    <div class="row align-items-end">
                        <x-field class="col-sm-6" label="Harga dasar (Rp)" name="harga_dasar" type="number" min="0" step="500"
                                 :value="$produk->harga_dasar ? (int) $produk->harga_dasar : null" hint="Patokan internal." />
                        <div class="col-sm-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tampilkan_harga" name="tampilkan_harga" value="1"
                                       @checked(old('tampilkan_harga', $produk->tampilkan_harga))>
                                <label class="form-check-label" for="tampilkan_harga">Tampilkan harga di katalog publik</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1"
                                       @checked(old('is_aktif', $produk->is_aktif))>
                                <label class="form-check-label" for="is_aktif">Tampil di katalog</label>
                            </div>
                        </div>
                    </div>

                    <x-field label="Deskripsi untuk Google" name="meta_deskripsi" :value="$produk->meta_deskripsi" maxlength="160"
                             hint="Maksimal 160 karakter. Kosongkan untuk memakai awal deskripsi." />
                </x-panel>

                <x-panel judul="Foto">
                    <div class="d-flex gap-3 align-items-start">
                        @if ($produk->foto_utama)
                            <img src="{{ Storage::url($produk->foto_utama) }}" alt="" class="thumb" style="width:80px;height:100px">
                        @endif
                        <x-field class="flex-grow-1" label="Foto utama" name="foto_utama" type="file" accept="image/*"
                                 hint="{{ $produk->foto_utama ? 'Pilih file baru untuk mengganti.' : 'Rasio 4:5 paling pas.' }}" />
                    </div>
                    <x-field label="Tambah foto galeri" name="galeri" hint="Boleh pilih beberapa sekaligus.">
                        <input type="file" id="galeri" name="galeri[]" accept="image/*" multiple class="form-control">
                        @error('galeri.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </x-field>
                </x-panel>

                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary">{{ $produk->exists ? 'Simpan perubahan' : 'Simpan & isi komposisi' }}</button>
                    <a href="{{ route('admin.produk.index') }}" class="btn btn-polos">Kembali</a>
                </div>
            </form>

            @if ($produk->exists && $produk->foto->isNotEmpty())
                <x-panel judul="Galeri">
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($produk->foto as $foto)
                            <div class="text-center">
                                <img src="{{ Storage::url($foto->file) }}" alt="" class="thumb d-block mb-1" style="width:80px;height:100px">
                                <form method="POST" action="{{ route('admin.produk.foto.hapus', [$produk, $foto]) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-polos btn-sm">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </x-panel>
            @endif
        </div>

        <div class="col-lg-5">
            @if ($produk->exists)
                @php
                    $barisKomposisi = old('komposisi', $produk->komposisi->map(fn ($k) => ['bahan_id' => $k->bahan_id, 'jumlah' => (float) $k->jumlah])->all());
                @endphp
                <form method="POST" action="{{ route('admin.produk.komposisi', $produk) }}">
                    @csrf @method('PUT')
                    <x-panel judul="Komposisi bahan (untuk 1 buket)">
                        <p class="small text-ink-2">Dipakai untuk cek kelayakan pesanan dan menghitung modal. Boleh dikosongkan — produk tetap bisa dipesan.</p>

                        @if ($errors->has('komposisi.*'))
                            <div class="alert alert-danger py-2">{{ $errors->first('komposisi.*') }}</div>
                        @endif

                        <div id="daftar-komposisi">
                            @foreach ($barisKomposisi as $i => $baris)
                                <div class="d-flex gap-2 mb-2 baris-komposisi">
                                    <select name="komposisi[{{ $i }}][bahan_id]" class="form-select" required>
                                        <option value="">Pilih bahan</option>
                                        @foreach ($semuaBahan as $b)
                                            <option value="{{ $b->id }}" @selected($baris['bahan_id'] == $b->id)>{{ $b->nama }} ({{ $b->satuan }})</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="komposisi[{{ $i }}][jumlah]" value="{{ $baris['jumlah'] }}"
                                           step="0.01" min="0.01" class="form-control angka" style="width:6.5rem" required aria-label="Jumlah">
                                    <button type="button" class="btn btn-polos hapus-baris" aria-label="Hapus baris">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <template id="templat-komposisi">
                            <div class="d-flex gap-2 mb-2 baris-komposisi">
                                <select name="komposisi[__i__][bahan_id]" class="form-select" required>
                                    <option value="">Pilih bahan</option>
                                    @foreach ($semuaBahan as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan }})</option>
                                    @endforeach
                                </select>
                                <input type="number" name="komposisi[__i__][jumlah]" step="0.01" min="0.01" class="form-control angka" style="width:6.5rem" required aria-label="Jumlah">
                                <button type="button" class="btn btn-polos hapus-baris" aria-label="Hapus baris">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                </button>
                            </div>
                        </template>

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" id="tambah-baris" class="btn btn-garis">Tambah bahan</button>
                            <button type="submit" class="btn btn-primary">Simpan komposisi</button>
                        </div>
                    </x-panel>
                </form>

                <script>
                    const daftar = document.getElementById('daftar-komposisi');
                    let nomor = {{ count($barisKomposisi) + 100 }};

                    document.getElementById('tambah-baris').addEventListener('click', () => {
                        const html = document.getElementById('templat-komposisi').innerHTML.replaceAll('__i__', nomor++);
                        daftar.insertAdjacentHTML('beforeend', html);
                    });

                    daftar.addEventListener('click', (e) => {
                        const tombol = e.target.closest('.hapus-baris');
                        if (tombol) tombol.closest('.baris-komposisi').remove();
                    });
                </script>
            @else
                <x-panel judul="Komposisi bahan">
                    <p class="text-ink-2 mb-0">Simpan produk dulu, lalu isi bahan apa saja yang dipakai untuk merakit satu buket.</p>
                </x-panel>
            @endif
        </div>
    </div>
@endsection

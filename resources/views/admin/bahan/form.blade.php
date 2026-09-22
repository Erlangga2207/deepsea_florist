@extends('layouts.admin')

@section('judul', $bahan->exists ? 'Ubah bahan' : 'Tambah bahan')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $bahan->exists ? 'Ubah '.$bahan->nama : 'Tambah bahan' }}</h1>
        @if ($bahan->exists)
            <form method="POST" action="{{ route('admin.bahan.destroy', $bahan) }}" onsubmit="return confirm('Hapus {{ $bahan->nama }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-polos">Hapus bahan</button>
            </form>
        @endif
    </div>

    <form method="POST" action="{{ $bahan->exists ? route('admin.bahan.update', $bahan) : route('admin.bahan.store') }}" style="max-width:640px">
        @csrf
        @if ($bahan->exists) @method('PUT') @endif

        <x-panel>
            <x-field label="Nama bahan" name="nama" :value="$bahan->nama" wajib autofocus />

            <div class="row">
                <x-field class="col-sm-6" label="Jenis" name="jenis" wajib>
                    <select id="jenis" name="jenis" class="form-select" required>
                        @foreach ($jenis as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(old('jenis', $bahan->jenis) === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field class="col-sm-6" label="Satuan" name="satuan" :value="$bahan->satuan" list="pilihan-satuan" wajib />
                <datalist id="pilihan-satuan">
                    @foreach (['tangkai', 'ikat', 'lembar', 'meter', 'roll', 'pcs'] as $s)
                        <option value="{{ $s }}">
                    @endforeach
                </datalist>
            </div>

            <div class="row">
                <x-field class="col-sm-6" label="Stok minimum" name="stok_minimum" type="number" step="0.01" min="0"
                         :value="$bahan->stok_minimum ?? 0" wajib hint="Di bawah angka ini bahan ditandai menipis." />
                <x-field class="col-sm-6" label="Harga beli terakhir (Rp)" name="harga_beli_terakhir" type="number" step="1" min="0"
                         :value="$bahan->harga_beli_terakhir !== null ? (int) $bahan->harga_beli_terakhir : 0" wajib hint="Per satuan. Dipakai menghitung modal." />
            </div>

            @if ($bahan->exists)
                <p class="text-ink-2">
                    Sisa stok sekarang: <strong class="text-body">@angka($bahan->stok) {{ $bahan->satuan }}</strong>.
                    Untuk mengubahnya, <a href="{{ route('admin.mutasi-stok.create', ['bahan' => $bahan->id]) }}">catat stok masuk/keluar</a>.
                </p>
            @else
                <x-field label="Stok awal" name="stok_awal" type="number" step="0.01" min="0"
                         hint="Jumlah yang ada sekarang. Akan dicatat sebagai stok masuk." />
            @endif

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="dijual_eceran" name="dijual_eceran" value="1"
                       @checked(old('dijual_eceran', $bahan->dijual_eceran))>
                <label class="form-check-label" for="dijual_eceran">Juga dijual eceran per {{ $bahan->satuan ?: 'satuan' }}</label>
            </div>
            <x-field label="Harga eceran (Rp)" name="harga_eceran" type="number" step="1" min="0"
                     :value="$bahan->harga_eceran ? (int) $bahan->harga_eceran : null" hint="Isi bila dijual eceran." />

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1" @checked(old('is_aktif', $bahan->is_aktif))>
                <label class="form-check-label" for="is_aktif">Aktif (muncul di pilihan komposisi)</label>
            </div>
        </x-panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.bahan.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

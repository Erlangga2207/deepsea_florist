@extends('layouts.admin')

@section('judul', $kategori->exists ? 'Ubah kategori' : 'Tambah kategori')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $kategori->exists ? 'Ubah kategori' : 'Tambah kategori' }}</h1>
    </div>

    <form method="POST" action="{{ $kategori->exists ? route('admin.kategori.update', $kategori) : route('admin.kategori.store') }}" style="max-width:640px">
        @csrf
        @if ($kategori->exists) @method('PUT') @endif

        <x-panel>
            <x-field label="Nama" name="nama" :value="$kategori->nama" wajib autofocus />

            <x-field label="Rumus harga" name="tipe_harga" wajib
                     hint="Menentukan cara sistem menghitung saran harga. Nama kategori boleh diganti kapan saja, rumusnya tetap ikut pilihan ini.">
                <select id="tipe_harga" name="tipe_harga" class="form-select @error('tipe_harga') is-invalid @enderror" required>
                    @foreach ($tipeHarga as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('tipe_harga', $kategori->tipe_harga) === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </x-field>

            <div class="row">
                <x-field class="col-sm-8" label="Slug (alamat halaman)" name="slug" :value="$kategori->slug"
                         hint="Kosongkan supaya dibuat otomatis dari nama." />
                <x-field class="col-sm-4" label="Urutan tampil" name="urutan" type="number" min="0" :value="$kategori->urutan ?? 0" />
            </div>

            <x-field label="Paragraf pengantar" name="deskripsi" hint="Tampil di halaman kategori publik. 100–150 kata sudah cukup.">
                <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
            </x-field>

            <x-field label="Deskripsi untuk Google" name="meta_deskripsi" :value="$kategori->meta_deskripsi" maxlength="160"
                     hint="Maksimal 160 karakter." />
        </x-panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

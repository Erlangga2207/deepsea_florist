@extends('layouts.admin')

@section('judul', $pengguna->exists ? 'Ubah akun' : 'Tambah akun')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $pengguna->exists ? 'Ubah akun '.$pengguna->name : 'Tambah akun' }}</h1>
    </div>

    <form method="POST" action="{{ $pengguna->exists ? route('admin.pengguna.update', $pengguna) : route('admin.pengguna.store') }}" style="max-width:560px">
        @csrf
        @if ($pengguna->exists) @method('PUT') @endif

        <x-panel>
            <x-field label="Nama" name="name" :value="$pengguna->name" wajib autofocus />
            <x-field label="Email" name="email" type="email" :value="$pengguna->email" wajib hint="Dipakai untuk masuk ke panel." />

            <x-field label="Peran" name="role" wajib>
                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
                    <option value="karyawan" @selected(old('role', $pengguna->role) === 'karyawan')>Karyawan — pesanan, katalog, stok</option>
                    <option value="owner" @selected(old('role', $pengguna->role) === 'owner')>Pemilik — semua menu termasuk keuangan</option>
                </select>
            </x-field>

            <div class="row">
                <x-field class="col-sm-6" :label="$pengguna->exists ? 'Kata sandi baru' : 'Kata sandi'" name="password" type="password"
                         autocomplete="new-password" :wajib="! $pengguna->exists"
                         :hint="$pengguna->exists ? 'Isi hanya bila ingin mengganti. Minimal 8 karakter.' : 'Minimal 8 karakter.'" />
                <x-field class="col-sm-6" label="Ulangi kata sandi" name="password_confirmation" type="password"
                         autocomplete="new-password" :wajib="! $pengguna->exists" />
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1" @checked(old('is_aktif', $pengguna->is_aktif))>
                <label class="form-check-label" for="is_aktif">Akun aktif (bisa masuk)</label>
            </div>
            @error('is_aktif') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </x-panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

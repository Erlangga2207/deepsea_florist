@extends('layouts.admin')

@section('judul', 'Pengguna')

@section('konten')
    <div class="judul-halaman">
        <h1>Pengguna</h1>
        <a href="{{ route('admin.pengguna.create') }}" class="btn btn-primary">Tambah akun</a>
    </div>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($pengguna as $u)
                    <tr>
                        <td class="fw-semibold">
                            {{ $u->name }}
                            @if ($u->is(auth()->user())) <span class="small text-ink-2 fw-normal">(Anda)</span> @endif
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->role === 'owner' ? 'Pemilik' : 'Karyawan' }}</td>
                        <td>
                            <x-pill :warna="$u->is_aktif ? 'hijau' : 'neutral'" :label="$u->is_aktif ? 'Aktif' : 'Nonaktif'" />
                        </td>
                        <td class="text-end"><a href="{{ route('admin.pengguna.edit', $u) }}" class="btn btn-garis btn-sm">Ubah</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection

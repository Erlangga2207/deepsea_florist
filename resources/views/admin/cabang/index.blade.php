@extends('layouts.admin')

@section('judul', 'Cabang')

@section('konten')
    <div class="judul-halaman">
        <h1>Cabang</h1>
        <a href="{{ route('admin.cabang.create') }}" class="btn btn-primary">Tambah cabang</a>
    </div>

    <p class="text-ink-2">Hanya data lokasi untuk halaman Kontak, footer, dan Google. Stok dan pesanan tetap satu, di rumah produksi.</p>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Cabang</th>
                    <th>Alamat</th>
                    <th>Website</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($cabang as $c)
                    <tr class="baris-link">
                        <td class="angka text-start">{{ $c->urutan }}</td>
                        <td>
                            <a href="{{ route('admin.cabang.edit', $c) }}" class="stretched-link text-reset text-decoration-none fw-semibold d-block">{{ $c->nama }}</a>
                            @if ($c->is_utama) <x-pill warna="rose" label="Cabang utama" /> @endif
                        </td>
                        <td class="small">{{ $c->alamat }}</td>
                        <td>
                            <x-pill :warna="$c->tampil_di_website ? 'hijau' : 'neutral'" :label="$c->tampil_di_website ? 'Tampil' : 'Disembunyikan'" />
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.cabang.edit', $c) }}" class="btn btn-garis btn-sm">Ubah</a>
                            @unless ($c->is_utama)
                                <form method="POST" action="{{ route('admin.cabang.destroy', $c) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus cabang {{ $c->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-polos btn-sm">Hapus</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-ink-2 py-4">Belum ada cabang.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection

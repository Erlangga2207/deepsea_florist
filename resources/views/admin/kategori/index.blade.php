@extends('layouts.admin')

@section('judul', 'Kategori')

@section('konten')
    <div class="judul-halaman">
        <h1>Kategori</h1>
        <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary">Tambah kategori</a>
    </div>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Nama</th>
                    <th>Rumus harga</th>
                    <th class="angka">Produk</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($kategori as $k)
                    <tr>
                        <td class="angka text-start">{{ $k->urutan }}</td>
                        <td>
                            <div class="fw-semibold">{{ $k->nama }}</div>
                            <div class="small text-ink-2">/{{ $k->slug }}</div>
                        </td>
                        <td>{{ \App\Http\Controllers\Admin\KategoriController::TIPE_HARGA[$k->tipe_harga] }}</td>
                        <td class="angka">{{ $k->produk_count }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.kategori.edit', $k) }}" class="btn btn-garis btn-sm">Ubah</a>
                            <form method="POST" action="{{ route('admin.kategori.destroy', $k) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus kategori {{ $k->nama }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-polos btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-ink-2 py-4">Belum ada kategori.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection

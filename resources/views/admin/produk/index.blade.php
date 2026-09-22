@extends('layouts.admin')

@section('judul', 'Produk')

@section('konten')
    <div class="judul-halaman">
        <h1>Produk</h1>
        <a href="{{ route('admin.produk.create') }}" class="btn btn-primary">Tambah produk</a>
    </div>

    <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
        <input type="search" name="cari" value="{{ request('cari') }}" class="form-control" style="max-width:260px" placeholder="Cari nama atau kode">
        <select name="kategori" class="form-select" style="max-width:220px" onchange="this.form.submit()">
            <option value="">Semua kategori</option>
            @foreach ($kategori as $k)
                <option value="{{ $k->id }}" @selected(request('kategori') == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
        <button class="btn btn-garis">Cari</button>
    </form>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th></th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Komposisi</th>
                    <th class="angka">Harga dasar</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($produk as $p)
                    <tr>
                        <td>
                            @if ($p->foto_utama)
                                <img src="{{ Storage::url($p->foto_utama) }}" alt="" class="thumb">
                            @else
                                <div class="thumb"></div>
                            @endif
                        </td>
                        <td class="angka text-start">{{ $p->kode }}</td>
                        <td>
                            <a href="{{ route('admin.produk.show', $p) }}" class="fw-semibold text-body">{{ $p->nama }}</a>
                            <div class="small text-ink-2">{{ $p->kategori->nama }}</div>
                        </td>
                        <td class="text-nowrap">
                            <x-pill :warna="$p->status === 'ready' ? 'hijau' : 'rose'" :label="$p->status === 'ready' ? 'Ready' : 'Pre-order'" />
                            @unless ($p->is_aktif)
                                <x-pill label="Disembunyikan" />
                            @endunless
                        </td>
                        <td>
                            @if ($p->komposisi_count)
                                {{ $p->komposisi_count }} bahan
                            @else
                                <x-pill warna="amber" label="Belum diisi" />
                            @endif
                        </td>
                        <td class="angka">
                            @if ($p->harga_dasar) @rupiah($p->harga_dasar) @else — @endif
                        </td>
                        <td class="text-end"><a href="{{ route('admin.produk.edit', $p) }}" class="btn btn-garis btn-sm">Ubah</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-ink-2 py-4">Tidak ada produk yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>

    {{ $produk->links() }}
@endsection

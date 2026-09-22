@extends('layouts.admin')

@section('judul', 'Stok produk jadi')

@section('konten')
    <div class="judul-halaman">
        <h1>Stok produk jadi</h1>
    </div>

    <nav class="tab-garis">
        <a href="{{ route('admin.stok-produk-jadi.index') }}" class="{{ $status === 'tersedia' ? 'aktif' : '' }}">Tersedia</a>
        <a href="{{ route('admin.stok-produk-jadi.index', ['status' => 'terjual']) }}" class="{{ $status === 'terjual' ? 'aktif' : '' }}">Terjual</a>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <x-panel>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr><th></th><th>Buket</th><th>Asal</th><th class="angka">Harga jual</th><th></th></tr>
                        </thead>
                        <tbody>
                        @forelse ($stok as $s)
                            <tr>
                                <td><x-foto-produk :path="$s->foto" alt="" class="thumb" /></td>
                                <td>
                                    <div class="fw-semibold">{{ $s->nama }}</div>
                                    <div class="small text-ink-2">sejak {{ $s->created_at->translatedFormat('j M Y') }}</div>
                                </td>
                                <td>
                                    @if ($s->asal === 'batal')
                                        Pesanan batal
                                        @if ($s->pesanan) <a href="{{ route('admin.pesanan.show', $s->pesanan) }}" class="angka">{{ $s->pesanan->kode }}</a> @endif
                                    @else
                                        Dibuat untuk dipajang
                                    @endif
                                </td>
                                <td class="angka text-nowrap">@if ($s->harga_jual) @rupiah($s->harga_jual) @else — @endif</td>
                                <td class="text-end text-nowrap">
                                    <form method="POST" action="{{ route('admin.stok-produk-jadi.update', $s) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <button class="btn btn-garis btn-sm">{{ $s->status === 'tersedia' ? 'Tandai terjual' : 'Kembalikan' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.stok-produk-jadi.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Hapus catatan {{ $s->nama }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-polos btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-ink-2 py-4">Kosong.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-panel>
        </div>
        <div class="col-lg-4">
            <form method="POST" action="{{ route('admin.stok-produk-jadi.store') }}">
                @csrf
                <x-panel judul="Catat buket untuk dipajang">
                    <x-field label="Model" name="produk_id">
                        <select id="produk_id" name="produk_id" class="form-select">
                            <option value="">Di luar katalog</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}" @selected(old('produk_id') == $p->id)>{{ $p->kode }} · {{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Nama" name="nama" hint="Isi bila di luar katalog." />
                    <x-field label="Harga jual (Rp)" name="harga_jual" type="number" min="0" step="500" />
                    <button class="btn btn-primary w-100">Simpan</button>
                </x-panel>
            </form>
        </div>
    </div>
@endsection

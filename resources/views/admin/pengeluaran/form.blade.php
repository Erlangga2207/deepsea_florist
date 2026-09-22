@extends('layouts.admin')

@section('judul', $pengeluaran->exists ? 'Ubah pengeluaran' : 'Catat pengeluaran')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $pengeluaran->exists ? 'Ubah pengeluaran' : 'Catat pengeluaran' }}</h1>
    </div>

    <form method="POST" enctype="multipart/form-data" style="max-width:560px"
          action="{{ $pengeluaran->exists ? route('admin.pengeluaran.update', $pengeluaran) : route('admin.pengeluaran.store') }}">
        @csrf
        @if ($pengeluaran->exists) @method('PUT') @endif

        <x-panel>
            <div class="row">
                <x-field class="col-sm-6" label="Tanggal" name="tanggal" type="date" :value="$pengeluaran->tanggal?->toDateString()" wajib />
                <x-field class="col-sm-6" label="Kategori" name="kategori" wajib>
                    <select id="kategori" name="kategori" class="form-select">
                        @foreach ($kategori as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(old('kategori', $pengeluaran->kategori) === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>
            <x-field label="Nominal (Rp)" name="nominal" type="number" min="1" step="500" :value="$pengeluaran->nominal ? (int) $pengeluaran->nominal : null" wajib />
            <x-field label="Keterangan" name="keterangan" :value="$pengeluaran->keterangan" maxlength="200" placeholder="mis. Kulakan bunga fresh Lembang" />
            <x-field label="Foto nota / bukti" name="bukti" type="file" accept="image/*,application/pdf"
                     :hint="$pengeluaran->bukti ? 'Sudah ada bukti. Pilih file baru untuk mengganti.' : 'Foto nota atau PDF, maksimal 4 MB.'" />
        </x-panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.pengeluaran.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

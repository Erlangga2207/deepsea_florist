@extends('layouts.admin')

@section('judul', $faq->exists ? 'Ubah pertanyaan' : 'Tambah pertanyaan')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $faq->exists ? 'Ubah pertanyaan' : 'Tambah pertanyaan' }}</h1>
    </div>

    <form method="POST" action="{{ $faq->exists ? route('admin.faq.update', $faq) : route('admin.faq.store') }}" style="max-width:720px">
        @csrf
        @if ($faq->exists) @method('PUT') @endif

        <x-panel>
            <x-field label="Pertanyaan" name="pertanyaan" :value="$faq->pertanyaan" maxlength="255" wajib autofocus
                     hint="Tulis seperti yang biasa ditanyakan pelanggan di WhatsApp." />

            <x-field label="Jawaban" name="jawaban" wajib hint="Jangan menulis angka harga — harga dibicarakan lewat chat.">
                <textarea id="jawaban" name="jawaban" rows="6" class="form-control @error('jawaban') is-invalid @enderror" required>{{ old('jawaban', $faq->jawaban) }}</textarea>
            </x-field>

            <x-field label="Urutan tampil" name="urutan" type="number" min="0" :value="$faq->urutan ?? 0" style="max-width:140px" />

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1" @checked(old('is_aktif', $faq->is_aktif))>
                <label class="form-check-label" for="is_aktif">Tampilkan di halaman FAQ</label>
            </div>
        </x-panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.faq.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

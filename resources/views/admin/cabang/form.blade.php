@extends('layouts.admin')

@section('judul', $cabang->exists ? 'Ubah cabang' : 'Tambah cabang')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $cabang->exists ? 'Ubah cabang' : 'Tambah cabang' }}</h1>
    </div>

    <form method="POST" action="{{ $cabang->exists ? route('admin.cabang.update', $cabang) : route('admin.cabang.store') }}" style="max-width:640px">
        @csrf
        @if ($cabang->exists) @method('PUT') @endif

        <x-panel>
            <x-field label="Nama cabang" name="nama" :value="$cabang->nama" maxlength="100" wajib autofocus />

            <x-field label="Alamat" name="alamat" wajib>
                <textarea id="alamat" name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $cabang->alamat) }}</textarea>
            </x-field>

            <x-field label="Koordinat" name="koordinat" wajib placeholder="-6.5312, 107.7234"
                     :value="$cabang->exists ? $cabang->koordinat() : null"
                     hint="Buka Google Maps, tekan lama titik toko, salin angka koordinat, tempel di sini." />
            @foreach (['latitude', 'longitude'] as $sumbu)
                @error($sumbu) <div class="invalid-feedback d-block mt-n2 mb-3">{{ $message }}</div> @enderror
            @endforeach

            @if ($cabang->exists)
                <iframe class="peta mb-3" title="Pratinjau pin {{ $cabang->nama }}" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" src="{{ $cabang->embed_url }}"></iframe>
                <p class="small text-ink-2 mt-n2">Pratinjau dari koordinat yang tersimpan. Kalau pin meleset, ganti koordinat lalu simpan.</p>
            @endif

            <x-field label="Link Google Maps" name="url_maps" type="url" :value="$cabang->url_maps" placeholder="https://maps.app.goo.gl/…"
                     hint="Boleh kosong. Link profil toko di Google Maps, kalau ada." />

            <div class="row">
                <x-field class="col-sm-6" label="Nomor WhatsApp cabang" name="no_wa" :value="$cabang->no_wa" inputmode="numeric"
                         hint="Boleh kosong. Boleh ditulis 0812…" />
                <x-field class="col-sm-6" label="Urutan tampil" name="urutan" type="number" min="0" :value="$cabang->urutan ?? 0" />
            </div>

            <x-field label="Jam buka" name="jam_buka" :value="$cabang->jam_buka" maxlength="150" placeholder="Setiap hari 08.00–20.00" />

            <div class="form-check">
                <input class="form-check-input @error('is_utama') is-invalid @enderror" type="checkbox" id="is_utama" name="is_utama" value="1"
                       @checked(old('is_utama', $cabang->is_utama))>
                <label class="form-check-label" for="is_utama">Cabang utama</label>
                <div class="form-text">Alamat cabang utama dipakai di footer, Google, dan kop laporan. Cabang utama lama otomatis dilepas.</div>
                @error('is_utama') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input @error('tampil_di_website') is-invalid @enderror" type="checkbox" id="tampil_di_website" name="tampil_di_website" value="1"
                       @checked(old('tampil_di_website', $cabang->tampil_di_website))>
                <label class="form-check-label" for="tampil_di_website">Tampilkan di website</label>
                @error('tampil_di_website') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </x-panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.cabang.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

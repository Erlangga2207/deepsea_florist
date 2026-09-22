@extends('layouts.admin')

@section('judul', 'Pengaturan')

@section('konten')
    <div class="judul-halaman">
        <h1>Pengaturan</h1>
    </div>

    <form method="POST" action="{{ route('admin.pengaturan.update') }}" style="max-width:760px">
        @csrf @method('PUT')

        <x-panel judul="Data toko">
            <p class="small text-ink-2">Tampil di halaman publik: header, footer, dan tombol WhatsApp.</p>
            <x-field label="Nama toko" name="nama_toko" :value="$pengaturan->nama_toko" wajib />
            <x-field label="Alamat" name="alamat">
                <textarea id="alamat" name="alamat" rows="2" class="form-control">{{ old('alamat', $pengaturan->alamat) }}</textarea>
            </x-field>
            <div class="row">
                <x-field class="col-sm-6" label="Jam buka" name="jam_buka" :value="$pengaturan->jam_buka" />
                <x-field class="col-sm-6" label="Nomor WhatsApp" name="no_wa" :value="$pengaturan->no_wa" inputmode="numeric" wajib
                         hint="Boleh ditulis 0812… — otomatis diubah ke 62812…" />
            </div>
            <div class="row">
                <x-field class="col-sm-6" label="Link Instagram" name="link_ig" type="url" :value="$pengaturan->link_ig" placeholder="https://instagram.com/…" />
                <x-field class="col-sm-6" label="Link TikTok" name="link_tiktok" type="url" :value="$pengaturan->link_tiktok" placeholder="https://tiktok.com/@…" />
            </div>
            <x-field label="Tentang toko" name="tentang">
                <textarea id="tentang" name="tentang" rows="4" class="form-control">{{ old('tentang', $pengaturan->tentang) }}</textarea>
            </x-field>
        </x-panel>

        <x-panel judul="Hitungan harga & kapasitas">
            <div class="row">
                <x-field class="col-sm-6" label="Margin bahan" name="margin_default" type="number" step="0.01" min="1"
                         :value="(float) $pengaturan->margin_default" wajib hint="1,60 artinya modal bahan × 1,6." />
                <x-field class="col-sm-6" label="Tarif jasa per jam (Rp)" name="tarif_jasa_per_jam" type="number" min="0"
                         :value="(int) $pengaturan->tarif_jasa_per_jam" wajib />
            </div>
            <div class="row">
                <x-field class="col-sm-6" label="Jumlah perakit" name="jumlah_perakit" type="number" min="1"
                         :value="$pengaturan->jumlah_perakit" wajib />
                <x-field class="col-sm-6" label="Jam kerja per hari (per orang)" name="jam_kerja_per_hari" type="number" step="0.5" min="0.5"
                         :value="(float) $pengaturan->jam_kerja_per_hari" wajib
                         hint="Kapasitas harian = perakit × jam kerja." />
            </div>
        </x-panel>

        <x-panel judul="Google (SEO)">
            <x-field label="Judul default" name="meta_judul_default" :value="$pengaturan->meta_judul_default" maxlength="70" />
            <x-field label="Deskripsi default" name="meta_deskripsi_default" hint="Maksimal 160 karakter.">
                <textarea id="meta_deskripsi_default" name="meta_deskripsi_default" rows="2" maxlength="160" class="form-control">{{ old('meta_deskripsi_default', $pengaturan->meta_deskripsi_default) }}</textarea>
            </x-field>
            <div class="row">
                <x-field class="col-sm-6" label="Latitude kios" name="latitude" type="number" step="0.0000001" :value="$pengaturan->latitude" />
                <x-field class="col-sm-6" label="Longitude kios" name="longitude" type="number" step="0.0000001" :value="$pengaturan->longitude" />
            </div>
            <x-field label="Link Google Bisnis Profil" name="google_maps_url" type="url" :value="$pengaturan->google_maps_url" />
        </x-panel>

        <button type="submit" class="btn btn-primary">Simpan pengaturan</button>
    </form>
@endsection

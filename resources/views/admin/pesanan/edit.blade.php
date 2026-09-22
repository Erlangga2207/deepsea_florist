@extends('layouts.admin')

@section('judul', 'Ubah '.$pesanan->kode)

@section('konten')
    @php $item = $pesanan->item->first(); @endphp

    <div class="judul-halaman">
        <div>
            <h1>Ubah pesanan <span class="angka">{{ $pesanan->kode }}</span></h1>
            <div class="text-ink-2">{{ $item->nama_item }} × {{ $item->qty }} — model dan jumlah tidak bisa diubah karena terkait stok bahan.</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.pesanan.update', $pesanan) }}" style="max-width:760px">
        @csrf @method('PUT')

        <x-panel judul="Pemesan">
            <div class="row">
                <x-field class="col-sm-6" label="Nama pemesan" name="nama_pelanggan" :value="$pesanan->pelanggan->nama" wajib />
                <x-field class="col-sm-6" label="Kontak" name="kontak" :value="$pesanan->pelanggan->kontak" />
            </div>
            <x-field label="Sumber pesanan" name="sumber" wajib>
                <select id="sumber" name="sumber" class="form-select">
                    @foreach ($sumber as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('sumber', $pesanan->pelanggan->sumber) === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </x-field>
        </x-panel>

        <x-panel judul="Pesanan">
            <div class="row">
                <x-field class="col-sm-4" label="Ukuran" name="ukuran" :value="$item->ukuran" />
                <x-field class="col-sm-4" label="Warna" name="warna" :value="$item->warna" />
                <x-field class="col-sm-4" label="Harga satuan (Rp)" name="harga" type="number" min="0" step="500" :value="(int) $item->harga" wajib />
            </div>
            <x-field label="Kartu ucapan" name="kartu_ucapan">
                <textarea id="kartu_ucapan" name="kartu_ucapan" rows="2" class="form-control">{{ old('kartu_ucapan', $item->kartu_ucapan) }}</textarea>
            </x-field>
            <div class="row">
                <x-field class="col-sm-6" label="Tanggal jadi" name="tanggal_jadi" type="date" :value="$pesanan->tanggal_jadi->toDateString()" wajib />
                <x-field class="col-sm-6" label="Diambil atau diantar" name="metode_ambil" wajib>
                    <select id="metode_ambil" name="metode_ambil" class="form-select">
                        <option value="ambil" @selected(old('metode_ambil', $pesanan->metode_ambil) === 'ambil')>Diambil di toko</option>
                        <option value="antar" @selected(old('metode_ambil', $pesanan->metode_ambil) === 'antar')>Diantar</option>
                    </select>
                </x-field>
            </div>
            <div class="row">
                <x-field class="col-sm-8" label="Alamat antar" name="alamat_antar" :value="$pesanan->alamat_antar" />
                <x-field class="col-sm-4" label="Ongkir (Rp)" name="ongkir" type="number" min="0" step="1000" :value="(int) $pesanan->ongkir" />
            </div>
            <div class="row">
                <x-field class="col-sm-4" label="Biaya tambahan (Rp)" name="biaya_tambahan" type="number" min="0" step="1000" :value="(int) $pesanan->biaya_tambahan" />
                <x-field class="col-sm-8" label="Catatan" name="catatan" :value="$pesanan->catatan" />
            </div>
        </x-panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.pesanan.show', $pesanan) }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

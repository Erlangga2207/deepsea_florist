@extends('layouts.admin')

@section('judul', 'Catat stok')

@section('konten')
    <div class="judul-halaman">
        <h1>Catat stok masuk / keluar</h1>
        <a href="{{ route('admin.mutasi-stok.index') }}" class="btn btn-garis">Riwayat stok</a>
    </div>

    <form method="POST" action="{{ route('admin.mutasi-stok.store') }}" style="max-width:640px">
        @csrf

        <x-panel>
            <x-field label="Bahan" name="bahan_id" wajib>
                <select id="bahan_id" name="bahan_id" class="form-select" required>
                    <option value="">Pilih bahan</option>
                    @foreach ($semuaBahan as $b)
                        <option value="{{ $b->id }}" data-jenis="{{ $b->jenis }}" data-satuan="{{ $b->satuan }}" data-stok="{{ (float) $b->stok }}"
                                @selected(old('bahan_id', $bahanDipilih) == $b->id)>{{ $b->nama }}</option>
                    @endforeach
                </select>
                <div id="info-stok" class="form-text"></div>
            </x-field>

            <div class="mb-3">
                <div class="form-label">Jenis catatan</div>
                <div class="d-flex flex-wrap gap-3">
                    @foreach (['masuk' => 'Masuk (belanja)', 'keluar' => 'Keluar (dipakai / rusak)', 'penyesuaian' => 'Penyesuaian (hitung ulang)'] as $nilai => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe" id="tipe-{{ $nilai }}" value="{{ $nilai }}"
                                   @checked(old('tipe', 'masuk') === $nilai)>
                            <label class="form-check-label" for="tipe-{{ $nilai }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row">
                <x-field class="col-sm-6" label="Jumlah" name="jumlah" type="number" step="0.01" min="0" wajib
                         hint="Untuk penyesuaian, isi jumlah stok sebenarnya setelah dihitung." />
                <x-field class="col-sm-6" label="Tanggal" name="tanggal" type="date" :value="today()->toDateString()" wajib />
            </div>

            <div class="row" id="khusus-masuk">
                <x-field class="col-sm-6" label="Harga beli per satuan (Rp)" name="harga_beli" type="number" min="0"
                         hint="Dipakai untuk harga beli terakhir dan nominal pengeluaran." />
                <x-field class="col-sm-6" label="Layu sekitar tanggal" name="tanggal_kadaluarsa" type="date"
                         :value="today()->addDays(14)->toDateString()" hint="Khusus bunga fresh, biasanya ±2 minggu." />
                {{-- Tidak otomatis: stok satu angka per bahan (bukan per kulakan) dan tanggal layu hanya perkiraan,
                     jadi yang menentukan berapa yang benar-benar layu tetap orang yang melihat bunganya. --}}
                <div class="col-12 mb-3" id="khusus-fresh">
                    <div class="form-check">
                        <input type="hidden" name="buang_layu" value="0">
                        <input class="form-check-input" type="checkbox" id="buang_layu" name="buang_layu" value="1" @checked(old('buang_layu'))>
                        <label class="form-check-label" for="buang_layu">Buang sisa stok lama yang sudah layu</label>
                    </div>
                    <div id="isian-layu" class="mt-2" style="max-width:240px">
                        <label class="form-label" for="jumlah_layu">Jumlah layu dibuang</label>
                        <input type="number" step="0.01" min="0" id="jumlah_layu" name="jumlah_layu" value="{{ old('jumlah_layu') }}"
                               class="form-control @error('jumlah_layu') is-invalid @enderror">
                        @error('jumlah_layu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-text">Terisi seluruh sisa stok lama; kurangi kalau sebagian masih bagus. Dicatat sebagai stok keluar "bunga layu".</div>
                </div>
                @if (auth()->user()->role === 'owner')
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <input type="hidden" name="catat_pengeluaran" value="0">
                            <input class="form-check-input" type="checkbox" id="catat_pengeluaran" name="catat_pengeluaran" value="1"
                                   @checked(old('catat_pengeluaran', true))>
                            <label class="form-check-label" for="catat_pengeluaran">Catat juga sebagai pengeluaran (bahan)</label>
                        </div>
                        <div class="form-text">Jumlah × harga beli. Hilangkan centang kalau notanya dicatat sendiri di menu Pengeluaran.</div>
                    </div>
                @endif
            </div>

            <x-field label="Keterangan" name="keterangan" maxlength="200" placeholder="mis. kulakan Lembang, bunga layu"
                     hint="Wajib untuk penyesuaian." />
        </x-panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.bahan.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>

    <script>
        const pilihBahan = document.getElementById('bahan_id');
        const khususMasuk = document.getElementById('khusus-masuk');
        const kadaluarsa = document.getElementById('tanggal_kadaluarsa').closest('.col-sm-6');
        const khususFresh = document.getElementById('khusus-fresh');
        const buangLayu = document.getElementById('buang_layu');
        const jumlahLayu = document.getElementById('jumlah_layu');

        function perbarui() {
            const opsi = pilihBahan.selectedOptions[0];
            const tipe = document.querySelector('input[name="tipe"]:checked').value;
            document.getElementById('info-stok').textContent = opsi.value
                ? `Sisa sekarang: ${opsi.dataset.stok.replace('.', ',')} ${opsi.dataset.satuan}`
                : '';
            khususMasuk.hidden = tipe !== 'masuk';
            kadaluarsa.hidden = khususFresh.hidden = opsi.dataset.jenis !== 'fresh';
            document.getElementById('isian-layu').hidden = !buangLayu.checked;
        }

        // Saat dicentang atau ganti bahan, isi awal = seluruh sisa stok lama (tidak minus).
        function isiJumlahLayu() {
            if (buangLayu.checked) jumlahLayu.value = Math.max(0, parseFloat(pilihBahan.selectedOptions[0].dataset.stok || 0));
        }

        buangLayu.addEventListener('change', () => { isiJumlahLayu(); perbarui(); });
        pilihBahan.addEventListener('change', isiJumlahLayu);
        pilihBahan.addEventListener('change', perbarui);
        document.querySelectorAll('input[name="tipe"]').forEach(r => r.addEventListener('change', perbarui));
        perbarui();
    </script>
@endsection

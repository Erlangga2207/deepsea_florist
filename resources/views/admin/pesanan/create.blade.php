@extends('layouts.admin')

@section('judul', 'Pesanan baru')

@section('konten')
    <div class="judul-halaman">
        <h1>Pesanan baru</h1>
    </div>

    <div class="row g-4">
        {{-- Kiri: isian manusia --}}
        <div class="col-lg-7">
            <form method="POST" action="{{ route('admin.pesanan.store') }}" id="form-pesanan">
                @csrf

                <x-panel judul="Pemesan">
                    <div class="row">
                        <x-field class="col-sm-6" label="Nama pemesan" name="nama_pelanggan" wajib autofocus />
                        <x-field class="col-sm-6" label="Kontak" name="kontak" placeholder="No. WA / akun IG" />
                    </div>
                    <div class="mb-1">
                        <div class="form-label">Sumber pesanan</div>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach ($sumber as $nilai => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sumber" id="sumber-{{ $nilai }}" value="{{ $nilai }}" @checked(old('sumber', 'wa') === $nilai)>
                                    <label class="form-check-label" for="sumber-{{ $nilai }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-panel>

                <x-panel judul="Pesanan">
                    <x-field label="Model" name="produk_id" wajib>
                        <select id="produk_id" name="produk_id" class="form-select @error('produk_id') is-invalid @enderror">
                            <option value="" data-harga="">Model custom (di luar katalog)</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}" data-harga="{{ (int) $p->harga_dasar }}" @selected(old('produk_id', request('produk')) == $p->id)>
                                    {{ $p->kode }} · {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>
                    <div id="baris-custom">
                        <x-field label="Nama model custom" name="nama_item" wajib placeholder="mis. Buket uang 50 lembar pita emas" />
                    </div>

                    <div class="row">
                        <x-field class="col-6 col-sm-4" label="Ukuran" name="ukuran" value="Normal" />
                        <x-field class="col-6 col-sm-4" label="Warna" name="warna" />
                        <x-field class="col-sm-4" label="Jumlah" name="qty" type="number" min="1" value="1" wajib />
                    </div>
                    <x-field label="Kartu ucapan" name="kartu_ucapan">
                        <textarea id="kartu_ucapan" name="kartu_ucapan" rows="2" class="form-control">{{ old('kartu_ucapan') }}</textarea>
                    </x-field>

                    <div class="row">
                        <x-field class="col-sm-6" label="Tanggal jadi" name="tanggal_jadi" type="date" :min="today()->toDateString()" wajib />
                        <x-field class="col-sm-6" label="Harga satuan (Rp)" name="harga" type="number" min="0" step="500" wajib
                                 hint="Terisi dari harga dasar; ubah sesuai kesepakatan." />
                    </div>

                    <div class="mb-3">
                        <div class="form-label">Diambil atau diantar</div>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_ambil" id="ambil" value="ambil" @checked(old('metode_ambil', 'ambil') === 'ambil')>
                                <label class="form-check-label" for="ambil">Diambil di toko</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_ambil" id="antar" value="antar" @checked(old('metode_ambil') === 'antar')>
                                <label class="form-check-label" for="antar">Diantar</label>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="baris-antar">
                        <x-field class="col-sm-8" label="Alamat antar" name="alamat_antar" />
                        <x-field class="col-sm-4" label="Ongkir (Rp)" name="ongkir" type="number" min="0" step="1000" />
                    </div>

                    <div class="row">
                        <x-field class="col-sm-4" label="Biaya tambahan (Rp)" name="biaya_tambahan" type="number" min="0" step="1000"
                                 hint="Ukuran custom / kerumitan." />
                        <x-field class="col-sm-8" label="Catatan" name="catatan" placeholder="mis. Wisuda Polsub" />
                    </div>
                </x-panel>

                <x-panel judul="DP">
                    <div class="row align-items-start">
                        <x-field class="col-sm-6" label="DP diterima (Rp)" name="dp_jumlah" type="number" min="0" step="500" hint="" />
                        <x-field class="col-sm-6" label="Cara bayar" name="dp_metode">
                            <select id="dp_metode" name="dp_metode" class="form-select">
                                <option value="">—</option>
                                @foreach (['transfer' => 'Transfer', 'qris' => 'QRIS', 'cash' => 'Tunai'] as $nilai => $label)
                                    <option value="{{ $nilai }}" @selected(old('dp_metode') === $nilai)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>
                    <div class="small text-ink-2" id="info-total"></div>
                </x-panel>

                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary">Simpan pesanan</button>
                    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-polos">Batal</a>
                </div>
            </form>
        </div>

        {{-- Kanan: jawaban sistem --}}
        <div class="col-lg-5">
            <div id="jawab-bahan"></div>
            <div id="jawab-saran"></div>
            <div id="jawab-kapasitas"></div>
            <div id="jawab-harga">
                <x-panel judul="Perkiraan harga">
                    <p class="text-ink-2 mb-0">Pilih model untuk melihat perkiraan harga dan cek bahannya.</p>
                </x-panel>
            </div>
        </div>
    </div>

    {{-- Jawaban sistem: dihitung ulang tiap kali model, jumlah, atau tanggal berubah --}}
    <script>
        (() => {
            const f = document.getElementById('form-pesanan').elements;
            const el = id => document.getElementById(id);
            const esc = s => String(s ?? '').replace(/[&<>"]/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'}[c]));
            const rp = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
            const angka = n => (+n).toLocaleString('id-ID', {maximumFractionDigits: 2});
            const opsiHarga = {};
            let jeda;

            async function cek() {
                const data = {
                    produk_id: f.produk_id.value || null,
                    qty: f.qty.value || 1,
                    tanggal_jadi: f.tanggal_jadi.value || null,
                    ...opsiHarga,
                };
                const res = await fetch(@json(route('admin.pesanan.cek')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify(data),
                });
                if (!res.ok) return;
                const j = await res.json();
                tampilBahan(j.kelayakan);
                tampilKapasitas(j.kapasitas);
                tampilHarga(j.harga, j.kelayakan);
            }

            function tampilBahan(k) {
                if (!k || k.layak) {
                    el('jawab-bahan').innerHTML = '';
                    el('jawab-saran').innerHTML = '';
                    return;
                }
                el('jawab-bahan').innerHTML = `<div class="peringatan peringatan-bad" role="status">
                    <strong>${esc(k.pesan)}</strong>Pesanan tetap bisa disimpan; bahan bisa dibeli dulu atau tawarkan model lain.</div>`;

                el('jawab-saran').innerHTML = !k.saran.length ? '' : `<section class="kotak panel">
                    <header class="panel-header"><h2>Model lain yang bahannya ada</h2></header>
                    <div class="panel-isi py-1">${k.saran.map(s => `
                        <div class="saran-item">
                            ${s.foto ? `<img src="${esc(s.foto)}" alt="" class="thumb">` : '<div class="thumb"></div>'}
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${esc(s.nama)} ${s.kode ? `<span class="text-ink-2 fw-normal small">${esc(s.kode)}</span>` : ''}</div>
                                <div class="small text-ink-2">${esc(s.keterangan)}</div>
                            </div>
                            ${s.produk_id ? `<button type="button" class="btn btn-garis btn-sm" data-pakai="${s.produk_id}">Pakai</button>` : ''}
                        </div>`).join('')}
                    </div></section>`;
            }

            function tampilKapasitas(k) {
                el('jawab-kapasitas').innerHTML = !k || !k.penuh ? '' : `<div class="peringatan peringatan-warn" role="status">
                    <strong>Tanggal ini sudah padat</strong>${esc(k.pesan)}
                    ${k.tanggal_saran ? `<div class="mt-2"><button type="button" class="btn btn-garis btn-sm" data-tanggal="${k.tanggal_saran}">Pakai tanggal itu</button></div>` : ''}
                </div>`;
            }

            function tampilHarga(h, k) {
                if (!h) return;
                const tanpaHitungan = k.tanpa_komposisi && !['uang', 'papan'].includes(h.tipe);
                const isian = {
                    uang: [['jumlah_lembar', 'Jumlah lembar'], ['tarif_lipat', 'Tarif lipat / lembar']],
                    papan: [['sewa_rangka', 'Sewa rangka'], ['ongkos_pasang', 'Ongkos pasang']],
                }[h.tipe] || [];
                const baris = (label, nilai, kelas = '') => `<tr class="${kelas}"><td>${label}</td><td class="angka">${nilai}</td></tr>`;

                el('jawab-harga').innerHTML = `<section class="kotak panel">
                    <header class="panel-header"><h2>Perkiraan harga</h2></header>
                    <div class="panel-isi">
                    ${tanpaHitungan
                        ? `<p class="mb-0 text-ink-2">Model ini belum punya komposisi bahan, jadi sistem belum bisa menghitung. Isi harga manual seperti biasa.</p>`
                        : `<p class="small text-ink-2 mb-2">${esc(h.label)}: ${esc(h.rumus)}</p>
                        ${isian.length ? `<div class="row g-2 mb-2">${isian.map(([nama, label]) => `
                            <div class="col-6"><label class="form-label small" for="opsi-${nama}">${label}</label>
                            <input type="number" min="0" class="form-control form-control-sm opsi-harga" id="opsi-${nama}" data-opsi="${nama}" value="${esc(opsiHarga[nama] ?? '')}"></div>`).join('')}</div>` : ''}
                        <table class="table table-sm"><tbody>
                            ${baris('Modal bahan', rp(h.modal_bahan))}
                            ${h.rumus.includes('margin') ? baris('× margin ' + angka(h.margin), rp(h.modal_bahan * h.margin)) : ''}
                            ${Object.entries(h.tambahan).map(([l, v]) => baris(esc(l), rp(v))).join('')}
                            ${baris(`Ongkos jasa <div class="small text-ink-2">${angka(h.estimasi_jam)} jam × tarif × kerumitan ${h.kerumitan}</div>`, rp(h.ongkos_jasa))}
                            ${baris('Saran harga / buket', rp(h.saran_harga), 'fw-bold')}
                        </tbody></table>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-garis btn-sm" data-pakai-harga="${Math.round(h.saran_harga)}">Pakai harga ini</button>
                            <span class="small text-ink-2">Ini saran, bukan harga mati.</span>
                        </div>`}
                    </div></section>`;
            }

            function cekNanti() { clearTimeout(jeda); jeda = setTimeout(cek, 250); }

            ['produk_id', 'qty', 'tanggal_jadi'].forEach(n => f[n].addEventListener('change', cekNanti));
            f.qty.addEventListener('input', cekNanti);

            document.addEventListener('input', e => {
                if (e.target.classList.contains('opsi-harga')) {
                    opsiHarga[e.target.dataset.opsi] = e.target.value;
                    clearTimeout(jeda);
                    jeda = setTimeout(async () => {
                        const fokus = e.target.id;
                        await cek();
                        const input = el(fokus);
                        input?.focus();
                    }, 400);
                }
            });

            document.addEventListener('click', e => {
                const t = e.target;
                if (t.dataset.pakai) {
                    f.produk_id.value = t.dataset.pakai;
                    f.produk_id.dispatchEvent(new Event('change', {bubbles: true}));
                } else if (t.dataset.tanggal) {
                    f.tanggal_jadi.value = t.dataset.tanggal;
                    f.tanggal_jadi.dispatchEvent(new Event('change', {bubbles: true}));
                } else if (t.dataset.pakaiHarga) {
                    f.harga.value = t.dataset.pakaiHarga;
                    f.harga.dispatchEvent(new Event('input', {bubbles: true}));
                }
            });

            if (f.produk_id.value || f.tanggal_jadi.value) cek();
        })();
    </script>

    <script>
        const form = document.getElementById('form-pesanan');
        const pilihProduk = document.getElementById('produk_id');
        const inputHarga = document.getElementById('harga');
        const rupiah = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

        function tampilkanBagian() {
            document.getElementById('baris-custom').hidden = pilihProduk.value !== '';
            form.elements.nama_item.required = pilihProduk.value === '';
            document.getElementById('baris-antar').hidden = !document.getElementById('antar').checked;
        }

        function hitungTotal() {
            const antar = document.getElementById('antar').checked;
            const total = (+inputHarga.value || 0) * (+form.elements.qty.value || 1)
                + (+form.elements.biaya_tambahan.value || 0)
                + (antar ? (+form.elements.ongkir.value || 0) : 0);
            document.getElementById('info-total').textContent = total > 0
                ? `Total ${rupiah(total)} · DP 50% = ${rupiah(total / 2)}`
                : '';
        }

        pilihProduk.addEventListener('change', () => {
            const harga = pilihProduk.selectedOptions[0].dataset.harga;
            if (harga && +harga > 0) inputHarga.value = harga;
            tampilkanBagian();
            hitungTotal();
        });
        form.addEventListener('input', hitungTotal);
        form.addEventListener('change', () => { tampilkanBagian(); hitungTotal(); });

        tampilkanBagian();
        if (pilihProduk.value && !inputHarga.value) pilihProduk.dispatchEvent(new Event('change'));
        hitungTotal();
    </script>
@endsection

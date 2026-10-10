@php
    $metode = ['transfer' => 'Transfer', 'qris' => 'QRIS', 'cash' => 'Tunai'];
    $lunas = $pesanan->sisaTagihan() <= 0;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota {{ $pesanan->kode }}</title>
    {{-- Isi nota dirancang muat di setengah atas A4, sisanya dipotong dan dipakai lagi --}}
    <style>
        @page { size: A4 portrait; margin: 12mm 14mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #2B2226; line-height: 1.3; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 2px 4px; vertical-align: top; text-align: left; }
        .kop td { padding: 0; vertical-align: middle; }
        .kop h1 { font-size: 14px; margin: 0; color: #8E2B44; }
        .abu { color: #6B5C61; }
        .kanan { text-align: right; }
        .judul { font-size: 13px; font-weight: bold; color: #B23A57; letter-spacing: 1px; }
        .garis { border-bottom: 1.5px solid #B23A57; margin: 5px 0 6px; }
        .info td { padding: 1px 4px 1px 0; }
        .item { margin-top: 6px; }
        .item th { font-size: 8px; text-transform: uppercase; color: #6B5C61; border-bottom: 1px solid #E9DEDB; }
        .item td { border-bottom: 1px solid #E9DEDB; }
        .total td { padding: 1px 4px; }
        .total .tebal td { font-weight: bold; border-top: 1px solid #2B2226; padding-top: 3px; }
        .cap { display: inline-block; border: 1.5px solid #B23A57; color: #B23A57; font-weight: bold; padding: 2px 8px; font-size: 11px; letter-spacing: 1px; }
        .cap-belum { border-color: #9A6414; color: #9A6414; }
        .kecil { font-size: 8px; }
        tr, .blok { page-break-inside: avoid; }
        .potong { margin-top: 10px; border-top: 1px dashed #6B5C61; text-align: center; page-break-inside: avoid; }
        .potong span { font-size: 7px; color: #6B5C61; }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            <td style="width: 46px"><img src="{{ public_path('img/pwa/icon-192.png') }}" width="40" height="40" alt=""></td>
            <td>
                <h1>{{ $pengaturan->nama_toko }}</h1>
                <div class="abu">{{ $cabangUtama?->alamat }} · WA {{ preg_replace('/^62/', '0', $pengaturan->no_wa) }}</div>
            </td>
            <td class="kanan" style="width: 150px">
                <div class="judul">NOTA PESANAN</div>
                <div><strong>{{ $pesanan->kode }}</strong></div>
            </td>
        </tr>
    </table>
    <div class="garis"></div>

    <table class="info">
        <tr>
            <td style="width: 50%">
                <span class="abu">Pemesan</span><br>
                <strong>{{ $pesanan->pelanggan->nama }}</strong>
                @if ($pesanan->pelanggan->kontak) · {{ $pesanan->pelanggan->kontak }} @endif
            </td>
            <td style="width: 25%"><span class="abu">Tanggal pesan</span><br>{{ $pesanan->tanggal_pesan->translatedFormat('j M Y') }}</td>
            <td style="width: 25%"><span class="abu">Tanggal jadi</span><br><strong>{{ $pesanan->tanggal_jadi->translatedFormat('j M Y') }}</strong></td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="abu">Pengambilan:</span>
                {{ $pesanan->metode_ambil === 'antar' ? 'Diantar ke '.($pesanan->alamat_antar ?: '(alamat belum diisi)') : 'Diambil di toko' }}
            </td>
        </tr>
    </table>

    <table class="item">
        <thead>
            <tr><th>Pesanan</th><th class="kanan" style="width: 30px">Qty</th><th class="kanan" style="width: 80px">Harga</th><th class="kanan" style="width: 85px">Jumlah</th></tr>
        </thead>
        <tbody>
        @foreach ($pesanan->item as $item)
            <tr>
                <td>
                    {{ $item->nama_item }}
                    <span class="abu">· {{ $item->ukuran ?: '—' }} · {{ $item->warna ?: '—' }}</span>
                    @if ($item->kartu_ucapan)
                        <div class="abu kecil">Kartu: “{{ $item->kartu_ucapan }}”</div>
                    @endif
                </td>
                <td class="kanan">{{ $item->qty }}</td>
                <td class="kanan">@rupiah($item->harga)</td>
                <td class="kanan">@rupiah($item->subtotal)</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table style="margin-top: 4px">
        <tr>
            <td style="width: 55%; padding-left: 0">
                @if ($pesanan->catatan)
                    <div class="blok"><span class="abu">Catatan:</span> {{ $pesanan->catatan }}</div>
                @endif
                <div style="margin-top: 8px">
                    @if ($lunas)
                        <span class="cap">LUNAS</span>
                    @else
                        <span class="cap cap-belum">BELUM LUNAS</span>
                        <div class="kecil abu" style="margin-top: 3px">Pelunasan wajib sebelum barang diserahkan.</div>
                    @endif
                </div>
            </td>
            <td style="padding-right: 0">
                <table class="total">
                    <tr><td>Subtotal</td><td class="kanan">@rupiah($pesanan->subtotal)</td></tr>
                    @if ($pesanan->biaya_tambahan > 0)
                        <tr><td>Biaya tambahan</td><td class="kanan">@rupiah($pesanan->biaya_tambahan)</td></tr>
                    @endif
                    @if ($pesanan->ongkir > 0)
                        <tr><td>Ongkir</td><td class="kanan">@rupiah($pesanan->ongkir)</td></tr>
                    @endif
                    <tr class="tebal"><td>Total</td><td class="kanan">@rupiah($pesanan->total)</td></tr>
                    @foreach ($pesanan->pembayaran as $b)
                        <tr class="abu">
                            <td>{{ $b->jenis === 'dp' ? 'DP' : 'Pelunasan' }} · {{ $metode[$b->metode] }} · {{ $b->tanggal->translatedFormat('j M Y') }}</td>
                            <td class="kanan">− @rupiah($b->jumlah)</td>
                        </tr>
                    @endforeach
                    <tr class="tebal"><td>Sisa tagihan</td><td class="kanan">@rupiah($pesanan->sisaTagihan())</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="kecil abu" style="margin-top: 8px">Terima kasih telah memesan di {{ $pengaturan->nama_toko }}. Dicetak {{ now()->translatedFormat('j M Y H.i') }}.</div>

    <div class="potong"><span>✂ potong di sini</span></div>
</body>
</html>

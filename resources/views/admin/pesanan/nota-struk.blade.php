@php
    $metode = ['transfer' => 'Transfer', 'qris' => 'QRIS', 'cash' => 'Tunai'];
    $lunas = $pesanan->sisaTagihan() <= 0;
    $sempit = $ukuran === 'struk-58';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota {{ $pesanan->kode }}</title>
    {{-- Printer thermal hanya hitam: tanpa warna, satu kolom. Tinggi kertas dihitung di controller. --}}
    <style>
        @page { margin: 4mm {{ $sempit ? '2mm' : '4mm' }}; }
        body { font-family: DejaVu Sans, sans-serif; font-size: {{ $sempit ? '7px' : '8.5px' }}; color: #000; line-height: 1.3; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 0; vertical-align: top; }
        .tengah { text-align: center; }
        .kanan { text-align: right; }
        .toko { font-size: {{ $sempit ? '10px' : '12px' }}; font-weight: bold; }
        .garis { border-top: 1px dashed #000; margin: 4px 0; }
        .tebal td { font-weight: bold; }
        .item { margin-top: 3px; }
        .kecil { font-size: {{ $sempit ? '6px' : '7px' }}; }
    </style>
</head>
<body>
    <div class="tengah">
        <div class="toko">{{ $pengaturan->nama_toko }}</div>
        <div>{{ $cabangUtama?->alamat }}</div>
        <div>WA {{ preg_replace('/^62/', '0', $pengaturan->no_wa) }}</div>
    </div>
    <div class="garis"></div>

    <table>
        <tr><td>No.</td><td class="kanan"><strong>{{ $pesanan->kode }}</strong></td></tr>
        <tr><td>Pesan</td><td class="kanan">{{ $pesanan->tanggal_pesan->translatedFormat('j M Y') }}</td></tr>
        <tr><td>Jadi</td><td class="kanan"><strong>{{ $pesanan->tanggal_jadi->translatedFormat('j M Y') }}</strong></td></tr>
        <tr><td>Pemesan</td><td class="kanan">{{ $pesanan->pelanggan->nama }}</td></tr>
        @if ($pesanan->pelanggan->kontak)
            <tr><td></td><td class="kanan">{{ $pesanan->pelanggan->kontak }}</td></tr>
        @endif
    </table>
    <div>{{ $pesanan->metode_ambil === 'antar' ? 'Diantar ke '.($pesanan->alamat_antar ?: '(alamat belum diisi)') : 'Diambil di toko' }}</div>
    <div class="garis"></div>

    @foreach ($pesanan->item as $item)
        <div class="item">
            <div>{{ $item->nama_item }} · {{ $item->ukuran ?: '—' }} · {{ $item->warna ?: '—' }}</div>
            <table>
                <tr><td>{{ $item->qty }} × @rupiah($item->harga)</td><td class="kanan">@rupiah($item->subtotal)</td></tr>
            </table>
            @if ($item->kartu_ucapan)
                <div class="kecil">Kartu: “{{ $item->kartu_ucapan }}”</div>
            @endif
        </div>
    @endforeach
    <div class="garis"></div>

    <table>
        <tr><td>Subtotal</td><td class="kanan">@rupiah($pesanan->subtotal)</td></tr>
        @if ($pesanan->biaya_tambahan > 0)
            <tr><td>Biaya tambahan</td><td class="kanan">@rupiah($pesanan->biaya_tambahan)</td></tr>
        @endif
        @if ($pesanan->ongkir > 0)
            <tr><td>Ongkir</td><td class="kanan">@rupiah($pesanan->ongkir)</td></tr>
        @endif
        <tr class="tebal"><td>Total</td><td class="kanan">@rupiah($pesanan->total)</td></tr>
        @foreach ($pesanan->pembayaran as $b)
            <tr>
                <td>{{ $b->jenis === 'dp' ? 'DP' : 'Pelunasan' }} {{ $metode[$b->metode] }}<br><span class="kecil">{{ $b->tanggal->translatedFormat('j M Y') }}</span></td>
                <td class="kanan">− @rupiah($b->jumlah)</td>
            </tr>
        @endforeach
        <tr class="tebal"><td>Sisa tagihan</td><td class="kanan">@rupiah($pesanan->sisaTagihan())</td></tr>
    </table>
    <div class="garis"></div>

    <div class="tengah"><strong>{{ $lunas ? '*** LUNAS ***' : '*** BELUM LUNAS ***' }}</strong></div>
    @unless ($lunas)
        <div class="tengah kecil">Pelunasan wajib sebelum barang diserahkan.</div>
    @endunless

    @if ($pesanan->catatan)
        <div style="margin-top: 4px">Catatan: {{ $pesanan->catatan }}</div>
    @endif

    <div class="garis"></div>
    <div class="tengah kecil">Terima kasih telah memesan di {{ $pengaturan->nama_toko }}<br>Dicetak {{ now()->translatedFormat('j M Y H.i') }}</div>
</body>
</html>

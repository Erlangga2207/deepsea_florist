@props(['pesanan'])
@php [$tanggalTeks, $tanggalKelas] = $pesanan->tanggalJadiRelatif(); [$bayarLabel, $bayarWarna] = $pesanan->statusBayar(); @endphp
<tr class="baris-link">
    <td class="kartu-judul">
        <a href="{{ route('admin.pesanan.show', $pesanan) }}" class="stretched-link text-reset text-decoration-none fw-semibold">{{ $pesanan->item->first()?->nama_item ?? $pesanan->kode }}</a>
        <div class="small text-ink-2 fw-normal">{{ $pesanan->pelanggan->nama }}@if ($pesanan->catatan) · {{ Str::limit($pesanan->catatan, 40) }}@endif · <span class="angka">{{ $pesanan->kode }}</span></div>
    </td>
    <td class="text-nowrap kartu-sebaris {{ $tanggalKelas }}">{{ $tanggalTeks }}</td>
    <td class="text-nowrap kartu-status"><x-pill :warna="$pesanan->warnaStatus()" :label="$pesanan->labelStatus()" /></td>
    <td class="text-nowrap kartu-sebaris"><x-pill :warna="$bayarWarna" :label="$bayarLabel" /></td>
</tr>

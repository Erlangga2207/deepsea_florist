@extends('layouts.admin')

@section('judul', 'Pengeluaran')

@section('konten')
    <div class="judul-halaman">
        <h1>Pengeluaran</h1>
        <a href="{{ route('admin.pengeluaran.create') }}" class="btn btn-primary">Catat pengeluaran</a>
    </div>

    <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
        <input type="month" name="bulan" value="{{ $bulan->format('Y-m') }}" class="form-control" style="max-width:200px" onchange="this.form.submit()" aria-label="Bulan">
        <select name="kategori" class="form-select" style="max-width:200px" onchange="this.form.submit()" aria-label="Kategori">
            <option value="">Semua kategori</option>
            @foreach ($kategori as $nilai => $label)
                <option value="{{ $nilai }}" @selected(request('kategori') === $nilai)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <x-panel :judul="'Total '.$bulan->translatedFormat('F Y')">
        <x-slot:aksi><strong class="angka">@rupiah($pengeluaran->sum('nominal'))</strong></x-slot:aksi>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th class="angka">Nominal</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($pengeluaran as $p)
                    <tr>
                        <td class="text-nowrap">{{ $p->tanggal->translatedFormat('j M Y') }}</td>
                        <td>{{ $kategori[$p->kategori] }}</td>
                        <td>
                            {{ $p->keterangan ?: '—' }}
                            @if ($p->bukti) <a href="{{ Storage::url($p->bukti) }}" target="_blank" rel="noopener" class="small">(bukti)</a> @endif
                        </td>
                        <td class="angka text-nowrap">@rupiah($p->nominal)</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.pengeluaran.edit', $p) }}" class="btn btn-garis btn-sm">Ubah</a>
                            <form method="POST" action="{{ route('admin.pengeluaran.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Hapus pengeluaran ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-polos btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-ink-2 py-4">Belum ada pengeluaran di bulan ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection

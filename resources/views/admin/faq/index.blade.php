@extends('layouts.admin')

@section('judul', 'FAQ')

@section('konten')
    <div class="judul-halaman">
        <h1>FAQ</h1>
        <a href="{{ route('admin.faq.create') }}" class="btn btn-primary">Tambah pertanyaan</a>
    </div>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Pertanyaan</th>
                    <th>Tampil</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($faq as $f)
                    <tr @class(['text-ink-2' => ! $f->is_aktif])>
                        <td class="angka text-start">{{ $f->urutan }}</td>
                        <td>
                            <div class="fw-semibold">{{ $f->pertanyaan }}</div>
                            <div class="small text-ink-2">{{ Str::limit($f->jawaban, 90) }}</div>
                        </td>
                        <td>
                            @if ($f->is_aktif)
                                <x-pill warna="hijau" label="Tampil" />
                            @else
                                <x-pill warna="neutral" label="Disembunyikan" />
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.faq.edit', $f) }}" class="btn btn-garis btn-sm">Ubah</a>
                            <form method="POST" action="{{ route('admin.faq.destroy', $f) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus pertanyaan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-polos btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-ink-2 py-4">Belum ada pertanyaan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection

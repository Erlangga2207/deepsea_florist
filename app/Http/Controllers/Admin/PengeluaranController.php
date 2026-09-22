<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengeluaranRequest;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        // Format bulan: 2026-09
        $bulan = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('bulan'))
            ? Carbon::createFromFormat('Y-m-d', $request->query('bulan').'-01')
            : today()->startOfMonth();

        $pengeluaran = Pengeluaran::with('user')
            ->whereBetween('tanggal', [$bulan->toDateString(), $bulan->copy()->endOfMonth()->toDateString()])
            ->when($request->kategori, fn ($q, $k) => $q->where('kategori', $k))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return view('admin.pengeluaran.index', [
            'pengeluaran' => $pengeluaran,
            'bulan' => $bulan,
            'kategori' => Pengeluaran::KATEGORI,
        ]);
    }

    public function create()
    {
        return view('admin.pengeluaran.form', ['pengeluaran' => new Pengeluaran(['tanggal' => today()]), 'kategori' => Pengeluaran::KATEGORI]);
    }

    public function store(PengeluaranRequest $request)
    {
        $data = $request->safe()->except('bukti');
        $data['user_id'] = $request->user()->id;
        $data['bukti'] = $request->file('bukti')?->store('bukti', 'public');

        $pengeluaran = Pengeluaran::create($data);

        return redirect()->route('admin.pengeluaran.index', ['bulan' => $pengeluaran->tanggal->format('Y-m')])
            ->with('sukses', 'Pengeluaran dicatat.');
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        return view('admin.pengeluaran.form', ['pengeluaran' => $pengeluaran, 'kategori' => Pengeluaran::KATEGORI]);
    }

    public function update(PengeluaranRequest $request, Pengeluaran $pengeluaran)
    {
        $data = $request->safe()->except('bukti');
        if ($request->hasFile('bukti')) {
            if ($pengeluaran->bukti) {
                Storage::disk('public')->delete($pengeluaran->bukti);
            }
            $data['bukti'] = $request->file('bukti')->store('bukti', 'public');
        }

        $pengeluaran->update($data);

        return redirect()->route('admin.pengeluaran.index', ['bulan' => $pengeluaran->tanggal->format('Y-m')])
            ->with('sukses', 'Pengeluaran diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        if ($pengeluaran->mutasiStok()->exists()) {
            return back()->with('gagal', 'Pengeluaran ini terhubung ke catatan stok masuk, jadi tidak bisa dihapus. Ubah nominalnya saja bila salah.');
        }

        if ($pengeluaran->bukti) {
            Storage::disk('public')->delete($pengeluaran->bukti);
        }
        $pengeluaran->delete();

        return back()->with('sukses', 'Pengeluaran dihapus.');
    }
}

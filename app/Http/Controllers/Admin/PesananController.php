<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Harga\HargaFactory;
use App\Domain\Jadwal\CekKapasitas;
use App\Domain\Pesanan\PesananService;
use App\Domain\Stok\CekKelayakan;
use App\Http\Controllers\Controller;
use App\Http\Requests\CekPesananRequest;
use App\Http\Requests\PembayaranRequest;
use App\Http\Requests\PesananStoreRequest;
use App\Http\Requests\PesananUpdateRequest;
use App\Http\Requests\UbahStatusRequest;
use App\Models\Pesanan;
use App\Models\Produk;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PesananController extends Controller
{
    public const SUMBER = ['wa' => 'WhatsApp', 'ig' => 'Instagram', 'tiktok' => 'TikTok', 'langsung' => 'Datang langsung'];

    public function __construct(private PesananService $service) {}

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'aktif');

        $pesanan = Pesanan::with('pelanggan', 'item')
            ->when($tab === 'aktif', fn ($q) => $q->aktif())
            ->when(array_key_exists($tab, Pesanan::STATUS), fn ($q) => $q->where('status', $tab))
            ->when($request->cari, fn ($q, $cari) => $q->where(fn ($q) => $q
                ->where('kode', 'like', "%{$cari}%")
                ->orWhereHas('pelanggan', fn ($q) => $q->where('nama', 'like', "%{$cari}%"))))
            ->orderBy('tanggal_jadi', in_array($tab, ['selesai', 'batal', 'semua']) ? 'desc' : 'asc')
            ->orderBy('id')
            ->paginate(30)
            ->withQueryString();

        $jumlah = Pesanan::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');

        return view('admin.pesanan.index', compact('pesanan', 'tab', 'jumlah'));
    }

    public function create()
    {
        return view('admin.pesanan.create', [
            'produk' => Produk::with('kategori')->where('is_aktif', true)->orderBy('nama')->get(),
            'sumber' => self::SUMBER,
        ]);
    }

    public function store(PesananStoreRequest $request)
    {
        try {
            $pesanan = $this->service->buat($request->validated(), $request->user());
        } catch (DomainException $e) {
            return back()->withInput()->with('gagal', $e->getMessage());
        }

        return redirect()->route('admin.pesanan.show', $pesanan)->with('sukses', "Pesanan {$pesanan->kode} tersimpan.");
    }

    // AJAX dari form pesanan baru: jawaban sistem untuk kolom kanan. Hanya memberi tahu, tidak memblokir.
    public function cek(CekPesananRequest $request, CekKelayakan $kelayakan, CekKapasitas $kapasitas)
    {
        $data = $request->validated();
        $qty = (int) ($data['qty'] ?? 1);
        $produk = empty($data['produk_id']) ? null : Produk::with('kategori')->find($data['produk_id']);
        $jawaban = ['kelayakan' => null, 'harga' => null, 'kapasitas' => null];

        if ($produk) {
            $hasil = $kelayakan->untukProduk($produk, $qty);
            $jawaban['kelayakan'] = [
                'layak' => $hasil->layak,
                'tanpa_komposisi' => $hasil->tanpaKomposisi,
                'pesan' => $hasil->pesan(),
                'saran' => $hasil->saranAlternatif,
            ];

            $jawaban['harga'] = HargaFactory::untuk($produk, $data)->rincian() + [
                'tipe' => $produk->kategori->tipe_harga,
                'estimasi_jam' => (float) $produk->estimasi_jam,
                'kerumitan' => $produk->faktor_kerumitan,
            ];
        }

        if (! empty($data['tanggal_jadi'])) {
            $hasil = $kapasitas->untukTanggal(Carbon::parse($data['tanggal_jadi']), (float) ($produk?->estimasi_jam ?? 0) * $qty);
            $jawaban['kapasitas'] = [
                'penuh' => $hasil->penuh,
                'pesan' => $hasil->pesan(),
                'tanggal_saran' => $hasil->tanggalSaran?->toDateString(),
            ];
        }

        return response()->json($jawaban);
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load('pelanggan', 'user', 'item.produk', 'pembayaran', 'mutasiStok.bahan', 'stokProdukJadi');

        return view('admin.pesanan.show', ['pesanan' => $pesanan, 'sumber' => self::SUMBER]);
    }

    public function edit(Pesanan $pesanan)
    {
        $pesanan->load('pelanggan', 'item');

        return view('admin.pesanan.edit', ['pesanan' => $pesanan, 'sumber' => self::SUMBER]);
    }

    public function update(PesananUpdateRequest $request, Pesanan $pesanan)
    {
        // Kolom opsional yang tidak terkirim dianggap kosong
        $data = $request->validated() + array_fill_keys(['kontak', 'ukuran', 'warna', 'kartu_ucapan', 'alamat_antar', 'catatan'], null);
        $antar = $data['metode_ambil'] === 'antar';

        $pesanan->pelanggan->update(['nama' => $data['nama_pelanggan'], 'kontak' => $data['kontak'], 'sumber' => $data['sumber']]);

        $item = $pesanan->item()->first();
        $item->update([
            'ukuran' => $data['ukuran'],
            'warna' => $data['warna'],
            'kartu_ucapan' => $data['kartu_ucapan'],
            'harga' => $data['harga'],
            'subtotal' => $item->qty * $data['harga'],
        ]);

        $pesanan->fill([
            'tanggal_jadi' => $data['tanggal_jadi'],
            'metode_ambil' => $data['metode_ambil'],
            'alamat_antar' => $antar ? $data['alamat_antar'] : null,
            'ongkir' => $antar ? ($data['ongkir'] ?? 0) : 0,
            'biaya_tambahan' => $data['biaya_tambahan'] ?? 0,
            'catatan' => $data['catatan'],
        ]);
        $pesanan->hitungUlangTotal();

        return redirect()->route('admin.pesanan.show', $pesanan)->with('sukses', 'Pesanan diperbarui.');
    }

    public function ubahStatus(UbahStatusRequest $request, Pesanan $pesanan)
    {
        try {
            $this->service->ubahStatus($pesanan, $request->validated('status'), $request->user(), $request->validated());
        } catch (DomainException $e) {
            return back()->with('gagal', $e->getMessage());
        }

        return back()->with('sukses', "Status {$pesanan->kode} sekarang: {$pesanan->fresh()->labelStatus()}.");
    }

    public function bayar(PembayaranRequest $request, Pesanan $pesanan)
    {
        try {
            $this->service->bayar($pesanan, $request->validated('jumlah'), $request->validated('metode'), $request->validated('tanggal'));
        } catch (DomainException $e) {
            return back()->withInput()->with('gagal', $e->getMessage());
        }

        return back()->with('sukses', 'Pembayaran dicatat.');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

// Isi dari docs/09-FAQ-DAN-HERO.md bagian 1.6. Kalau aturan bisnis berubah, perbarui juga di sini.
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faq = [
            ['Berapa lama buket bunga fresh bisa bertahan?',
                'Buket bunga segar biasanya bertahan 5 sampai 7 hari kalau dirawat dengan benar. Simpan di tempat sejuk, jauh dari sinar matahari langsung dan angin kipas. Kalau memakai floral foam, siram sedikit air setiap hari. Untuk acara yang jauh hari, buket artificial lebih aman karena tidak layu.'],
            ['Apa bedanya buket bunga fresh dan buket artificial?',
                "Buket fresh memakai bunga hidup seperti mawar, lily, krisan, dan baby's breath — wanginya nyata dan tampilannya alami, tetapi umurnya terbatas. Buket artificial memakai bunga kain atau plastik berkualitas yang bisa disimpan bertahun-tahun sebagai kenang-kenangan. Untuk wisuda dan hadiah, keduanya sama-sama sering dipesan."],
            ['Berapa lama pengerjaan buket, dan kapan sebaiknya pesan?',
                'Sebagian besar buket selesai dalam 1 sampai 3 hari. Untuk bunga papan biasanya perlu waktu lebih lama. Saat musim wisuda pesanan sangat ramai, jadi sebaiknya pesan paling lambat 3 hari sebelum tanggal dibutuhkan. Pesanan mendadak tetap bisa dicoba — tanyakan lewat WhatsApp, kami akan cek jadwal pengerjaan hari itu.'],
            ['Berapa harga buket bunga di Subang?',
                'Harga menyesuaikan jenis bunga, ukuran, dan tingkat kerumitan rangkaian. Karena itu harga tidak kami pasang di katalog. Pilih modelnya di katalog, lalu kirim kode modelnya lewat WhatsApp — kami akan langsung memberi harga untuk ukuran yang diinginkan.'],
            ['Apakah melayani buket wisuda untuk mahasiswa Polsub?',
                'Ya. Buket wisuda adalah pesanan yang paling sering kami kerjakan, termasuk untuk wisuda Politeknik Negeri Subang dan sekolah di sekitar Subang. Warna buket bisa disesuaikan dengan warna toga atau almamater. Pesan lebih awal karena musim wisuda selalu penuh.'],
            ['Apakah bisa diantar? Sampai daerah mana?',
                'Sebagian besar pesanan diambil langsung di kios kami di Cinangsi, Cibogo. Pengantaran bisa dilakukan ke Subang kota, Pagaden, dan sekitarnya. Ongkos kirim menyesuaikan jarak dan disepakati lewat chat sebelum pengerjaan dimulai.'],
            ['Bagaimana cara memesan bunga papan ucapan?',
                'Kirim lewat WhatsApp: teks ucapan lengkap, nama pengirim, tanggal dan jam dipasang, serta alamat lokasinya. Bunga papan perlu waktu pengerjaan lebih lama dan pemasangan di tempat, jadi sebaiknya dipesan beberapa hari sebelumnya.'],
            ['Apakah bisa minta warna dan ukuran khusus?',
                'Bisa. Ukuran di katalog adalah ukuran standar. Ukuran yang lebih besar atau permintaan warna tertentu tetap kami layani dengan penyesuaian harga. Kalau bunga yang diminta sedang tidak tersedia, kami akan menawarkan model lain dengan nuansa warna yang mirip.'],
            ['Bagaimana cara pembayarannya?',
                'Pemesanan dimulai dengan DP 50 persen, sisanya dilunasi setelah buket jadi dan sebelum diambil atau diantar. Pembayaran bisa lewat transfer bank, QRIS, atau tunai di tempat.'],
            ['Bunga segarnya dari mana?',
                'Bunga segar kami ambil langsung dari Lembang setiap minggu, jadi stoknya selalu baru saat dirangkai. Karena dikulak mingguan, ketersediaan jenis bunga tertentu bisa berbeda-beda. Tanyakan dulu lewat WhatsApp kalau menginginkan bunga tertentu.'],
            ['Apa itu buket uang dan buket snack?',
                'Buket uang adalah rangkaian berisi lembaran uang yang dilipat menjadi bentuk bunga — uangnya disiapkan pemesan, kami mengerjakan lipatan dan rangkaiannya. Buket snack berisi cokelat atau makanan ringan yang dirangkai seperti buket bunga. Keduanya populer untuk hadiah wisuda dan ulang tahun.'],
            ['Bagaimana merawat buket supaya lebih awet?',
                'Untuk buket fresh: jauhkan dari sinar matahari langsung, angin kipas, dan AC yang mengarah langsung; potong sedikit ujung tangkai kalau dipindah ke vas; ganti airnya setiap hari. Untuk buket artificial: cukup dibersihkan debunya dengan kuas lembut dan jangan disimpan di tempat lembap.'],
        ];

        // firstOrCreate: aman dijalankan ulang di hosting tanpa menimpa jawaban yang sudah diedit owner
        foreach ($faq as $i => [$pertanyaan, $jawaban]) {
            Faq::firstOrCreate(['pertanyaan' => $pertanyaan], ['jawaban' => $jawaban, 'urutan' => $i + 1]);
        }
    }
}

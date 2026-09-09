# Design UI/UX — POS Warung Ayam Bakar (Tanpa Login)

## 1. Prinsip Desain
- Aplikasi langsung membuka halaman Kasir.
- Tidak ada informasi user atau logout.
- Tombol besar dan mudah disentuh.
- Alur transaksi dibuat sesingkat mungkin.
- Tampilan harus nyaman untuk PC dan tablet.
- HTML, CSS, dan JavaScript dibuat sederhana tanpa framework frontend.

## 2. Layout Umum

```text
┌──────────────┬──────────────────────────────────────┐
│              │ Topbar: Nama Warung | Tanggal/Jam   │
│   SIDEBAR    ├──────────────────────────────────────┤
│              │                                      │
│ Logo         │              KONTEN                  │
│ Kasir        │                                      │
│ Histori      │                                      │
│ Laporan      │                                      │
│ Menu         │                                      │
│ Pengaturan   │                                      │
└──────────────┴──────────────────────────────────────┘
```

Tidak ada menu Login/Logout.

## 3. Sidebar
Urutan:
1. Kasir
2. Histori Pesanan
3. Laporan Penjualan
4. Menu Makanan
5. Pengaturan

Kasir menjadi halaman default.

## 4. Halaman Kasir

### Kategori
- Paket Ayam
- Tambahan
- Minuman

### Paket Ayam
- Ayam Bakar — Rp20.000
- Ayam Panggang Santan — Rp20.000
- Ayam Geprek — Rp20.000

Setiap kartu diberi keterangan:
`+ nasi + es teh`

### Tambahan
- Tempe Bakar — Rp5.000
- Tahu Bakar — Rp5.000
- Tambah Sambal — Rp3.000
- Nasi Putih — Rp3.000

### Minuman
- Es Teh — Rp5.000
- Es Jeruk — Rp5.000
- Teh Anget — Rp5.000

## 5. Keranjang
Keranjang menampilkan:
- nama menu
- harga
- qty
- tombol +/-
- subtotal
- hapus item
- total

Tombol `BAYAR` dibuat besar dan selalu mudah ditemukan.

## 6. Modal Pembayaran

```text
Total       Rp53.000
Uang Bayar  [Rp60.000]

Kembalian   Rp7.000

[ Selesaikan & Cetak Nota ]
```

Kembalian dihitung real-time dengan JavaScript, tetapi total final tetap divalidasi server.

## 7. Histori Pesanan
Tabel:
- No. Nota
- Waktu
- Total
- Status
- Aksi

Aksi:
- Lihat
- Cetak ulang
- Void

Filter berdasarkan tanggal.

## 8. Laporan Penjualan

### Hari Ini
Kartu:
- Total Omzet
- Jumlah Transaksi
- Rata-rata Transaksi

Tabel:
- menu terlaris
- jumlah terjual
- omzet menu

### Rekap Bulanan
- pilih bulan/tahun
- total omzet
- tren harian
- menu terlaris
- perbandingan bulan sebelumnya

Grafik dapat menggunakan canvas + JavaScript vanilla.

## 9. Menu Makanan
Tabel menu:
- nama
- kategori
- harga
- status
- aksi

Status:
- aktif
- habis
- nonaktif

Form:
- nama menu
- kategori
- harga
- status

## 10. Pengaturan
Owner dapat mengubah:
- nama warung
- alamat
- nomor telepon
- informasi yang tampil pada nota

## 11. Nota
Nota tidak menggunakan sidebar/topbar.

Isi:
- nama warung
- alamat
- nomor nota
- tanggal/jam
- daftar item
- qty
- harga
- subtotal
- total
- uang bayar
- kembalian
- ucapan terima kasih

Gunakan `print.css` untuk kertas 58mm/80mm.

## 12. Gaya Visual
Nuansa:
- merah bata/oranye sebagai aksen
- putih/krem sebagai latar
- abu gelap untuk teks
- sidebar gelap
- tombol utama kontras

Gunakan font sans-serif seperti Arial, Segoe UI, atau Poppins.

## 13. Responsif
- Tablet landscape: menu dan keranjang 2 kolom.
- Desktop: 2 kolom lebih lebar.
- HP: 1 kolom, keranjang dapat dibuat bottom-sheet.
- Tidak membutuhkan framework CSS.

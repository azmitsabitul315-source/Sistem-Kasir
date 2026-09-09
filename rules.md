# Business Rules & Validation Rules — POS Warung Ayam Bakar

## 1. Aturan Akses
1. Tidak ada login.
2. Tidak ada role.
3. Owner adalah satu-satunya pengguna.
4. Aplikasi sebaiknya dijalankan pada perangkat warung atau LAN/private.

## 2. Aturan Menu dan Harga

### Paket Ayam
1. Ayam Bakar = Rp20.000.
2. Ayam Panggang Santan = Rp20.000.
3. Ayam Geprek = Rp20.000.
4. Setiap paket sudah termasuk 1 nasi putih + 1 es teh.
5. Nasi dan es teh bawaan paket tidak menjadi item terpisah.

### Tambahan
6. Tempe Bakar = Rp5.000/porsi.
7. Tahu Bakar = Rp5.000/porsi.
8. Tambah Sambal = Rp3.000/porsi.
9. Nasi Putih tambahan = Rp3.000.

### Minuman
10. Es Teh = Rp5.000.
11. Es Jeruk = Rp5.000.
12. Teh Anget = Rp5.000.

13. Minuman/nasi tambahan dihitung sebagai item terpisah.
14. Menu berstatus aktif dapat dipesan.
15. Menu habis/nonaktif tidak dapat ditambahkan.
16. Menu yang pernah dipakai transaksi tidak boleh dihapus permanen.

## 3. Aturan Keranjang
1. Qty minimal 1.
2. Qty tidak boleh nol.
3. Qty tidak boleh negatif.
4. Keranjang harus memiliki minimal satu item saat pembayaran.
5. Harga yang digunakan saat penyimpanan harus diambil dan divalidasi dari database.

## 4. Aturan Pembayaran
1. Versi inti menggunakan pembayaran tunai.
2. Uang bayar harus >= total.
3. Jika uang kurang, transaksi ditolak.
4. Kembalian = `amount_paid - total`.
5. Untuk nominal, gunakan DECIMAL pada database.
6. Jangan menggunakan FLOAT untuk uang.

## 5. Aturan Transaksi
1. Setelah disimpan, transaksi tidak dapat diedit.
2. Transaksi hanya dapat di-void.
3. Void wajib memiliki alasan.
4. Transaksi void tetap disimpan.
5. Transaksi void tidak masuk omzet/laporan.
6. Nomor nota harus unik.
7. Format nomor nota: `INV-YYYYMMDD-XXXX`.
8. Counter nomor nota di-reset setiap hari.
9. Nama menu dan harga pada transaksi disimpan sebagai snapshot.

## 6. Aturan Perhitungan
```text
item_subtotal = price × qty
subtotal       = SUM(item_subtotal)
total          = subtotal - discount
```

Pada versi dasar:
- discount default = Rp0.
- total minimal Rp0.

## 7. Aturan Server
Frontend hanya membantu UX.
Server wajib:
- memvalidasi item
- memvalidasi status menu
- mengambil harga database
- menghitung ulang subtotal
- menghitung total
- memvalidasi pembayaran
- menghitung kembalian
- menyimpan transaksi dengan DB transaction

## 8. Aturan Laporan
1. Hanya status `selesai` yang dihitung.
2. Status `void` tidak dihitung.
3. Laporan harian berdasarkan tanggal `created_at`.
4. Zona waktu aplikasi menggunakan WIB.
5. Laporan bulanan dikelompokkan berdasarkan bulan dan tahun.
6. Menu terlaris berdasarkan jumlah qty terjual.

## 9. Aturan Histori
Histori harus mempertahankan:
- nomor nota
- waktu
- nama item
- harga saat transaksi
- qty
- subtotal
- total
- status
- alasan void jika ada

Perubahan harga menu di kemudian hari tidak boleh mengubah transaksi lama.

## 10. Backup
- Backup database minimal satu kali sehari.
- Backup manual minimal satu kali seminggu.
- Data transaksi tidak boleh dihapus permanen.

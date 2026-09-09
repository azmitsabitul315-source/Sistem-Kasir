# PRD — Sistem POS Warung Ayam Bakar

## 1. Latar Belakang
Warung membutuhkan sistem kasir untuk menggantikan pencatatan manual sehingga proses pesanan lebih cepat, mengurangi salah hitung, dan menyediakan histori serta laporan penjualan.

Kasir adalah owner sendiri. Karena itu aplikasi tidak membutuhkan login, autentikasi, multi-user, atau role.

## 2. Tujuan
- Mempercepat input pesanan.
- Mengurangi kesalahan perhitungan.
- Menghitung pembayaran dan kembalian otomatis.
- Menyimpan histori transaksi.
- Menyediakan laporan harian dan bulanan.
- Mempermudah pencetakan nota.

## 3. Target Pengguna
Hanya satu pengguna:
**Owner / Kasir**

Tidak ada:
- customer account
- admin account
- staff account
- multi-role

## 4. Menu dan Harga

### Paket Ayam
| Menu | Harga | Termasuk |
|---|---:|---|
| Ayam Bakar | Rp20.000 | nasi + es teh |
| Ayam Panggang Santan | Rp20.000 | nasi + es teh |
| Ayam Geprek | Rp20.000 | nasi + es teh |

### Tambahan
| Menu | Harga |
|---|---:|
| Tempe Bakar | Rp5.000 |
| Tahu Bakar | Rp5.000 |
| Tambah Sambal | Rp3.000 |
| Nasi Putih | Rp3.000 |

### Minuman
| Menu | Harga |
|---|---:|
| Es Teh | Rp5.000 |
| Es Jeruk | Rp5.000 |
| Teh Anget | Rp5.000 |

## 5. Aturan Paket
Harga paket ayam tetap Rp20.000.
Setiap paket otomatis mencakup:
- 1 nasi putih
- 1 es teh

Keduanya tidak dihitung lagi dalam total.

Jika pelanggan meminta:
- tambahan nasi
- tambahan minuman
- minuman lain sebagai tambahan

maka item tersebut dihitung terpisah.

## 6. Scope Fitur

### 6.1 Kasir
- daftar menu
- kategori
- tambah ke keranjang
- ubah qty
- hapus item
- catatan item
- hitung subtotal
- hitung total
- input pembayaran tunai
- hitung kembalian
- simpan transaksi
- nomor nota otomatis
- cetak nota
- void transaksi

### 6.2 Menu
- tambah
- edit
- ubah harga
- aktifkan
- tandai habis
- nonaktifkan

### 6.3 Histori
- daftar transaksi
- filter tanggal
- detail transaksi
- cetak ulang
- void

### 6.4 Laporan
- omzet hari ini
- jumlah transaksi
- rata-rata transaksi
- menu terlaris
- omzet bulanan
- tren harian
- perbandingan bulan sebelumnya

### 6.5 Pengaturan
- nama warung
- alamat
- nomor telepon
- informasi nota

## 7. Aturan Transaksi
- Qty minimal 1.
- Keranjang tidak boleh kosong.
- Total dihitung server-side.
- Pembayaran tunai harus >= total.
- Kembalian = pembayaran - total.
- Transaksi yang tersimpan tidak diedit.
- Void harus memiliki alasan.
- Transaksi void tidak dihitung laporan.
- Nama dan harga item disimpan sebagai snapshot.
- Nomor nota format `INV-YYYYMMDD-XXXX`.

## 8. Non-Functional
- UI sederhana.
- Tombol besar.
- Ramah tablet.
- Waktu input idealnya kurang dari satu menit.
- Data transaksi tidak boleh hilang.
- Backup rutin.

## 9. Out of Scope
- Login
- Multi-user
- Role
- Stok bahan baku
- Multi-cabang
- Payment gateway otomatis
- Aplikasi pelanggan
- Member/pelanggan
- Self-order

## 10. Success Metrics
- Semua transaksi tercatat digital.
- Owner dapat melihat omzet harian.
- Owner dapat melihat rekap bulanan.
- Kesalahan hitung manual berkurang.
- Nota dapat dicetak dengan cepat.

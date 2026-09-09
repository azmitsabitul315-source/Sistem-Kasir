# Arsitektur Sistem — POS Warung Ayam Bakar (CI4 + PHP Native + HTML/CSS/JS)

## 1. Gambaran Umum
Aplikasi web POS sederhana untuk satu warung. Kasir adalah owner sendiri sehingga tidak ada sistem login, autentikasi, multi-user, atau role.

Aplikasi dibangun dengan:
- Backend: CodeIgniter 4 + PHP
- Database: MySQL/MariaDB
- View: PHP View CI4 berisi HTML biasa
- Styling: CSS murni
- Interaktivitas: JavaScript vanilla
- Cetak nota: `window.print()` + CSS `@media print`

Alur utama:

```text
[Browser PC/Tablet Warung]
          |
          v HTTP
[CodeIgniter 4]
  - Kasir
  - Menu
  - Histori
  - Laporan
  - Nota
          |
          v
[MySQL/MariaDB]
```

## 2. Tech Stack

| Layer | Pilihan |
|---|---|
| Backend | CodeIgniter 4 + PHP |
| Database | MySQL/MariaDB |
| View | Native PHP View CI4 + HTML |
| Styling | CSS murni |
| JavaScript | Vanilla JavaScript |
| Auth | Tidak ada |
| Cetak | `window.print()` + print CSS |
| Server lokal | XAMPP/Laragon |

Tidak menggunakan React, Vue, Laravel Blade, Tailwind, atau framework frontend tambahan.

## 3. Akses Sistem
Saat URL aplikasi dibuka, route default langsung menuju halaman Kasir.

Tidak ada:
- login
- register
- logout
- users
- role
- permission berbasis akun

Karena aplikasi tanpa login, sebaiknya hanya dijalankan pada PC warung atau jaringan LAN/private.

## 4. Struktur Folder

```text
app/
├── Controllers/
│   ├── Kasir.php
│   ├── Menu.php
│   ├── Histori.php
│   ├── Laporan.php
│   └── Nota.php
├── Models/
│   ├── MenuModel.php
│   ├── OrderModel.php
│   ├── OrderItemModel.php
│   └── SettingModel.php
├── Views/
│   ├── layout/
│   │   ├── header.php
│   │   └── sidebar.php
│   ├── kasir/
│   │   └── index.php
│   ├── menu/
│   │   ├── index.php
│   │   └── form.php
│   ├── histori/
│   │   ├── index.php
│   │   └── detail.php
│   ├── laporan/
│   │   ├── harian.php
│   │   └── bulanan.php
│   └── nota/
│       └── cetak.php
└── Database/
    ├── Migrations/
    └── Seeds/

public/
├── css/
│   ├── style.css
│   └── print.css
└── js/
    ├── kasir.js
    └── laporan.js
```

## 5. Komponen Utama

### Kasir Controller
Bertugas:
- mengambil menu aktif
- menerima pesanan
- validasi item dan qty
- menghitung subtotal/total di server
- validasi pembayaran
- membuat nomor nota
- menyimpan `orders` dan `order_items` dalam DB transaction

### Menu Controller
CRUD menu:
- tambah
- edit
- ubah harga
- aktif
- habis
- nonaktif

Menu yang pernah dipakai transaksi tidak dihapus permanen.

### Histori Controller
Menampilkan:
- daftar transaksi
- filter tanggal
- detail transaksi
- cetak ulang
- void transaksi

### Laporan Controller
Menampilkan:
- omzet hari ini
- jumlah transaksi
- rata-rata transaksi
- menu terlaris
- rekap bulanan
- tren omzet harian

### Nota Controller
Menampilkan nota khusus print tanpa sidebar.

## 6. Alur Transaksi

1. Owner membuka aplikasi.
2. Halaman Kasir langsung tampil.
3. Owner memilih menu.
4. JavaScript menambah item ke keranjang.
5. Owner mengatur qty.
6. Klik Bayar.
7. Modal pembayaran muncul.
8. Masukkan uang tunai.
9. JavaScript menampilkan kembalian.
10. Server menghitung ulang seluruh harga.
11. Server menyimpan transaksi dalam database transaction.
12. Nomor nota dibuat.
13. Halaman nota tampil.
14. Owner dapat mencetak nota.

## 7. Aturan Paket
Tiga menu ayam berharga Rp20.000 dan sudah termasuk:
- 1 nasi putih
- 1 es teh

Nasi dan es teh bawaan paket tidak ditambahkan sebagai item terpisah.

Jika pelanggan meminta nasi/minuman tambahan, item tambahan masuk keranjang dan dihitung sesuai harga normal.

## 8. Keamanan
Walaupun tanpa login:
- gunakan CSRF CI4
- validasi semua request server-side
- jangan percaya total dari JavaScript
- gunakan DB transaction saat menyimpan transaksi
- jalankan lokal/LAN
- jangan mengekspos aplikasi tanpa proteksi ke internet publik

## 9. Backup
Database sebaiknya:
- backup otomatis minimal 1x sehari
- backup manual minimal mingguan ke media eksternal

## 10. Prinsip Implementasi
Sistem dibuat sederhana karena hanya satu owner/kasir. Tidak perlu menambahkan fitur multi-user, stok bahan baku, pelanggan/member, payment gateway otomatis, atau aplikasi customer pada versi ini.

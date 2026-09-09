# Database Schema — POS Warung Ayam Bakar (CI4, Tanpa Login)

## 1. Ringkasan Entitas
Database terdiri dari:
- `categories`
- `menu_items`
- `orders`
- `order_items`
- `settings`

Tidak ada tabel `users` karena aplikasi single-user tanpa autentikasi.

## 2. `categories`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK AI | ID |
| name | VARCHAR(50) | Paket Ayam/Tambahan/Minuman |
| sort_order | INT DEFAULT 0 | Urutan kategori |

Data:
```text
1 | Paket Ayam
2 | Tambahan
3 | Minuman
```

## 3. `menu_items`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK AI | ID |
| category_id | BIGINT FK | Relasi kategori |
| name | VARCHAR(100) | Nama menu |
| price | DECIMAL(12,2) | Harga |
| status | ENUM('aktif','habis','nonaktif') | Status |
| created_at | DATETIME | Waktu dibuat |
| updated_at | DATETIME | Waktu diubah |

### Seed Awal

| Kategori | Menu | Harga |
|---|---|---:|
| Paket Ayam | Ayam Bakar | 20000 |
| Paket Ayam | Ayam Panggang Santan | 20000 |
| Paket Ayam | Ayam Geprek | 20000 |
| Tambahan | Tempe Bakar | 5000 |
| Tambahan | Tahu Bakar | 5000 |
| Tambahan | Tambah Sambal | 3000 |
| Tambahan | Nasi Putih | 3000 |
| Minuman | Es Teh | 5000 |
| Minuman | Es Jeruk | 5000 |
| Minuman | Teh Anget | 5000 |

## 4. `orders`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK AI | ID transaksi |
| invoice_no | VARCHAR(30) UNIQUE | INV-YYYYMMDD-XXXX |
| subtotal | DECIMAL(12,2) | Total sebelum diskon |
| discount | DECIMAL(12,2) DEFAULT 0 | Diskon |
| total | DECIMAL(12,2) | Total akhir |
| payment_method | ENUM('tunai') | Metode pembayaran inti |
| amount_paid | DECIMAL(12,2) | Uang diterima |
| change_amount | DECIMAL(12,2) | Kembalian |
| status | ENUM('selesai','void') | Status |
| void_reason | VARCHAR(255) NULL | Alasan void |
| created_at | DATETIME | Waktu transaksi |

## 5. `order_items`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK AI | ID detail |
| order_id | BIGINT FK | Relasi transaksi |
| menu_item_id | BIGINT FK | Relasi menu |
| item_name | VARCHAR(100) | Snapshot nama |
| price | DECIMAL(12,2) | Snapshot harga |
| qty | INT | Jumlah |
| note | VARCHAR(255) NULL | Catatan |
| item_subtotal | DECIMAL(12,2) | price × qty |

Snapshot nama/harga diperlukan agar histori lama tetap benar jika menu kemudian diedit.

## 6. `settings`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK AI | ID |
| key | VARCHAR(50) UNIQUE | nama_warung/alamat/no_telp |
| value | TEXT | Nilai |

## 7. Relasi

```text
categories (1) ──< menu_items (banyak)

orders (1) ──< order_items (banyak)

menu_items (1) ──< order_items (banyak)
```

## 8. Query Laporan

### Omzet hari ini

```sql
SELECT
    COUNT(*) AS jumlah_transaksi,
    COALESCE(SUM(total), 0) AS total_omzet
FROM orders
WHERE status = 'selesai'
  AND DATE(created_at) = CURDATE();
```

### Omzet bulanan

```sql
SELECT
    DATE(created_at) AS tanggal,
    SUM(total) AS omzet_harian
FROM orders
WHERE status = 'selesai'
  AND YEAR(created_at) = :tahun
  AND MONTH(created_at) = :bulan
GROUP BY DATE(created_at)
ORDER BY tanggal;
```

### Menu terlaris

```sql
SELECT
    oi.item_name,
    SUM(oi.qty) AS total_terjual
FROM order_items oi
JOIN orders o ON o.id = oi.order_id
WHERE o.status = 'selesai'
  AND DATE(o.created_at) BETWEEN :start AND :end
GROUP BY oi.item_name
ORDER BY total_terjual DESC
LIMIT 10;
```

## 9. Catatan Implementasi
Nominal menggunakan DECIMAL agar tidak terjadi kesalahan pembulatan. Transaksi penyimpanan `orders` dan `order_items` harus menggunakan database transaction CI4.

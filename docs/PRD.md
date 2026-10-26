# PRD: Sistem Manajemen Inventaris Barang

Mata Kuliah Sistem Basis Data · Proyek Kelompok (5 orang) Versi 1.0 · Oktober 2026

## 1. Ringkasan Produk

Sistem Manajemen Inventaris Barang adalah aplikasi web untuk membantu pengelola gudang atau toko mencatat barang masuk, barang keluar, melakukan stok opname, memantau stok yang menipis, dan melihat laporan stok harian maupun bulanan. Aplikasi menggantikan pencatatan manual (buku atau spreadsheet) yang rawan salah hitung dan sulit ditelusuri.

Aplikasi dibangun dengan Laravel (PHP) dan MySQL, dengan antarmuka HTML, CSS, dan JavaScript. Fokus akademis proyek ini adalah perancangan basis data yang benar: relasi antar tabel, integritas data, transaksi, dan query laporan.

## 2. Latar Belakang dan Masalah

| Masalah | Dampak |
| --- | --- |
| Stok dicatat manual dan terpisah-pisah | Jumlah stok sering tidak cocok dengan kondisi fisik |
| Tidak ada peringatan stok menipis | Barang kehabisan sebelum sempat dipesan ulang |
| Riwayat keluar masuk barang sulit dilacak | Sulit mencari penyebab selisih stok |
| Laporan dibuat manual setiap periode | Memakan waktu dan rawan kesalahan |

## 3. Tujuan

**Tujuan produk**

- Stok barang selalu akurat dan diperbarui otomatis setiap ada transaksi.
- Pengguna dapat mengetahui barang yang perlu dipesan ulang tanpa memeriksa satu per satu.
- Laporan harian dan bulanan dapat dibuka kapan saja dalam hitungan detik.

**Tujuan akademik**

- Menerapkan normalisasi (minimal 3NF), primary key, foreign key, dan constraint.
- Menerapkan transaksi database (`DB::transaction`) agar stok dan riwayat selalu konsisten.
- Menunjukkan query SQL yang bermakna: JOIN, GROUP BY, agregasi, dan filter tanggal.

## 4. Ruang Lingkup

**Termasuk (in scope)**

- Autentikasi pengguna (login dan logout).
- CRUD data barang dan pemasok.
- Pencatatan barang masuk dan barang keluar.
- Stok opname dan penyesuaian stok.
- Alert stok menipis di dashboard.
- Laporan stok harian dan bulanan, dapat dicetak atau diekspor.

**Tidak termasuk (out of scope)**

- Modul kasir atau penjualan (POS) dan pembayaran.
- Integrasi barcode scanner atau perangkat keras.
- Multi-gudang dan multi-cabang.
- Aplikasi mobile native.
- Notifikasi email atau WhatsApp (opsional jika waktu tersisa).

## 5. Pengguna

| Peran | Deskripsi | Hak akses |
| --- | --- | --- |
| Admin | Pemilik atau pengelola utama | Semua fitur, termasuk kelola pengguna dan hapus data master |
| Staf Gudang | Petugas harian | Catat barang masuk dan keluar, stok opname, lihat laporan |

Untuk menjaga proyek tetap realistis, peran cukup dibedakan dengan satu kolom `role` pada tabel `users`.

## 6. Kebutuhan Fungsional

Prioritas: **P0** wajib ada, **P1** sebaiknya ada, **P2** opsional.

### 6.1 Autentikasi

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-01 | Pengguna dapat login dengan email dan password | P0 |
| FR-02 | Pengguna dapat logout | P0 |
| FR-03 | Halaman hanya dapat diakses setelah login (middleware auth) | P0 |
| FR-04 | Admin dapat menambah dan menonaktifkan akun staf | P2 |

### 6.2 Data Barang (items)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-10 | Menampilkan daftar barang dengan pencarian nama atau kode dan pagination | P0 |
| FR-11 | Menambah barang: kode, nama, kategori, satuan, harga beli, harga jual, stok minimum | P0 |
| FR-12 | Mengubah dan menghapus barang (barang yang sudah punya transaksi tidak boleh dihapus) | P0 |
| FR-13 | Kode barang bersifat unik | P0 |
| FR-14 | Stok awal diisi saat barang dibuat, selanjutnya hanya berubah lewat transaksi | P0 |

### 6.3 Data Pemasok (suppliers)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-20 | CRUD pemasok: nama, nomor telepon, alamat, email | P0 |
| FR-21 | Pemasok yang sudah dipakai di transaksi tidak boleh dihapus | P0 |

### 6.4 Barang Masuk (stock\_ins)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-30 | Mencatat barang masuk: tanggal, barang, pemasok, jumlah, harga beli, catatan | P0 |
| FR-31 | Stok barang bertambah otomatis setelah transaksi disimpan | P0 |
| FR-32 | Jumlah harus lebih dari 0 | P0 |
| FR-33 | Daftar riwayat barang masuk dengan filter tanggal, barang, dan pemasok | P0 |
| FR-34 | Membatalkan transaksi masuk (stok dikurangi kembali, dengan validasi stok tidak negatif) | P1 |

### 6.5 Barang Keluar (stock\_outs)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-40 | Mencatat barang keluar: tanggal, barang, jumlah, alasan (terjual, rusak, kedaluwarsa, lainnya), catatan | P0 |
| FR-41 | Stok berkurang otomatis setelah transaksi disimpan | P0 |
| FR-42 | Sistem menolak transaksi jika jumlah melebihi stok tersedia | P0 |
| FR-43 | Daftar riwayat barang keluar dengan filter | P0 |
| FR-44 | Membatalkan transaksi keluar (stok dikembalikan) | P1 |

### 6.6 Stok Opname

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-50 | Membuat sesi opname dengan memilih satu atau banyak barang | P0 |
| FR-51 | Sistem menampilkan stok sistem; pengguna mengisi stok fisik | P0 |
| FR-52 | Selisih dihitung otomatis (fisik dikurangi sistem) dan ditandai jika tidak nol | P0 |
| FR-53 | Saat dikonfirmasi, stok barang diperbarui sesuai stok fisik dan selisih tersimpan sebagai riwayat | P0 |
| FR-54 | Daftar riwayat opname per tanggal | P1 |

### 6.7 Alert Stok Menipis

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-60 | Barang dianggap menipis jika `stock <= min_stock` | P0 |
| FR-61 | Dashboard menampilkan jumlah dan daftar barang menipis | P0 |
| FR-62 | Badge jumlah barang menipis pada menu navigasi | P1 |
| FR-63 | Barang dengan stok 0 ditandai terpisah sebagai "Habis" | P1 |

### 6.8 Laporan

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-70 | Laporan stok harian: stok awal, masuk, keluar, stok akhir per barang pada tanggal yang dipilih | P0 |
| FR-71 | Laporan stok bulanan: stok awal bulan, total masuk, total keluar, stok akhir per barang | P0 |
| FR-72 | Filter laporan berdasarkan tanggal atau bulan dan kategori | P0 |
| FR-73 | Cetak laporan (print stylesheet) atau ekspor PDF/Excel | P1 |
| FR-74 | Grafik ringkas pergerakan masuk dan keluar di dashboard | P2 |

### 6.9 Dashboard

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-80 | Ringkasan: total jenis barang, total pemasok, transaksi masuk dan keluar hari ini, barang menipis | P0 |
| FR-81 | Daftar 5 transaksi terakhir | P1 |

## 7. Kebutuhan Non-Fungsional

| ID | Kategori | Kebutuhan |
| --- | --- | --- |
| NFR-01 | Integritas data | Seluruh perubahan stok memakai transaksi database; jika satu langkah gagal, semua dibatalkan |
| NFR-02 | Keamanan | Password di-hash (bcrypt bawaan Laravel), proteksi CSRF, validasi input di server, query memakai Eloquent atau query builder (anti SQL injection) |
| NFR-03 | Performa | Halaman daftar dimuat di bawah 2 detik untuk 1.000 barang; gunakan pagination dan index |
| NFR-04 | Kegunaan | Antarmuka sederhana, konsisten, mudah dipakai tanpa pelatihan |
| NFR-05 | Responsif | Dapat digunakan di laptop dan tablet |
| NFR-06 | Kompatibilitas | Chrome, Firefox, dan Edge versi terbaru |
| NFR-07 | Pemeliharaan | Struktur MVC Laravel, migration dan seeder tersedia agar database dapat dibuat ulang |

## 8. Rancangan Basis Data

Tabel inti dari tugas: `items`, `suppliers`, `stock_ins`, `stock_outs`. Agar fitur login dan stok opname berjalan, ditambahkan dua tabel pendukung: `users` (bawaan Laravel) dan `stock_opnames`.

### 8.1 Relasi Antar Tabel

- `suppliers` 1 — N `stock_ins` (satu pemasok punya banyak transaksi masuk)
- `items` 1 — N `stock_ins` (satu barang punya banyak transaksi masuk)
- `items` 1 — N `stock_outs` (satu barang punya banyak transaksi keluar)
- `items` 1 — N `stock_opnames` (satu barang punya banyak riwayat opname)
- `users` 1 — N `stock_ins`, `stock_outs`, `stock_opnames` (pencatat transaksi)

### 8.2 Struktur Tabel

**items**

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED, PK, AI |  |
| code | VARCHAR(30) | UNIQUE, kode barang |
| name | VARCHAR(150) | Nama barang |
| category | VARCHAR(80) | Kategori |
| unit | VARCHAR(20) | Satuan (pcs, kg, box, dll.) |
| buy\_price | DECIMAL(12,2) | Harga beli |
| sell\_price | DECIMAL(12,2) | Harga jual |
| stock | INT | Stok saat ini, default 0, tidak boleh negatif |
| min\_stock | INT | Batas stok menipis, default 5 |
| created\_at, updated\_at | TIMESTAMP |  |

**suppliers**

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED, PK, AI |  |
| name | VARCHAR(150) | Nama pemasok |
| phone | VARCHAR(20) | Nullable |
| email | VARCHAR(100) | Nullable |
| address | TEXT | Nullable |
| created\_at, updated\_at | TIMESTAMP |  |

**stock\_ins**

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED, PK, AI |  |
| item\_id | FK ke items.id | ON DELETE RESTRICT |
| supplier\_id | FK ke suppliers.id | ON DELETE RESTRICT |
| user\_id | FK ke users.id | Pencatat |
| date | DATE | Tanggal barang masuk |
| quantity | INT | Harus lebih dari 0 |
| buy\_price | DECIMAL(12,2) | Harga beli saat transaksi |
| note | VARCHAR(255) | Nullable |
| created\_at, updated\_at | TIMESTAMP |  |

**stock\_outs**

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED, PK, AI |  |
| item\_id | FK ke items.id | ON DELETE RESTRICT |
| user\_id | FK ke users.id | Pencatat |
| date | DATE | Tanggal barang keluar |
| quantity | INT | Harus lebih dari 0 |
| reason | ENUM('terjual','rusak','kedaluwarsa','lainnya') |  |
| note | VARCHAR(255) | Nullable |
| created\_at, updated\_at | TIMESTAMP |  |

**stock\_opnames** (tabel tambahan)

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED, PK, AI |  |
| item\_id | FK ke items.id |  |
| user\_id | FK ke users.id |  |
| date | DATE | Tanggal opname |
| system\_stock | INT | Stok menurut sistem saat opname |
| physical\_stock | INT | Stok hasil hitung fisik |
| difference | INT | physical\_stock dikurangi system\_stock |
| note | VARCHAR(255) | Nullable |
| created\_at, updated\_at | TIMESTAMP |  |

**users** (bawaan Laravel, ditambah kolom `role` ENUM('admin','staff'))

### 8.3 Index yang Disarankan

- `items.code` (unique), `items.name`, `items.category`
- `stock_ins.date`, `stock_outs.date`, `stock_opnames.date` (mempercepat laporan)
- Index pada seluruh foreign key

### 8.4 Aturan Integritas

- Stok tidak boleh negatif (validasi aplikasi dan `CHECK (stock >= 0)` pada MySQL 8).
- Foreign key memakai `RESTRICT` agar barang dan pemasok yang punya transaksi tidak terhapus.
- Setiap transaksi masuk, keluar, dan opname dijalankan di dalam `DB::transaction` dengan `lockForUpdate()` pada baris barang untuk mencegah selisih saat akses bersamaan.

## 9. Logika Bisnis Utama

**Barang masuk:** validasi input, lalu dalam satu transaksi: simpan ke `stock_ins`, tambah `items.stock` sebesar `quantity`.

**Barang keluar:** validasi input, kunci baris barang, cek `stock >= quantity`, simpan ke `stock_outs`, kurangi `items.stock`. Jika stok tidak cukup, tampilkan pesan error dan batalkan.

**Stok opname:** simpan `system_stock` dan `physical_stock`, hitung `difference`, lalu set `items.stock = physical_stock`.

**Alert stok menipis:** `SELECT * FROM items WHERE stock <= min_stock ORDER BY stock ASC`.

**Laporan harian** untuk tanggal D:

- Stok akhir = stok saat ini dikurangi seluruh masuk setelah D, ditambah seluruh keluar setelah D, dengan penyesuaian opname setelah D.
- Stok awal = stok akhir dikurangi masuk pada D, ditambah keluar pada D.

Cara yang lebih sederhana dan disarankan untuk proyek ini: hitung stok awal sebagai hasil akumulasi seluruh transaksi sebelum D, lalu tambahkan masuk dan kurangi keluar pada D. Selisih opname diperlakukan sebagai transaksi penyesuaian. Tim perlu memilih satu pendekatan dan konsisten di seluruh laporan.

**Contoh query laporan bulanan (ringkasan masuk dan keluar):**

```sql
SELECT i.code, i.name, i.unit,
       COALESCE(si.total_in, 0)  AS total_masuk,
       COALESCE(so.total_out, 0) AS total_keluar
FROM items i
LEFT JOIN (
    SELECT item_id, SUM(quantity) AS total_in
    FROM stock_ins
    WHERE date BETWEEN :start AND :end
    GROUP BY item_id
) si ON si.item_id = i.id
LEFT JOIN (
    SELECT item_id, SUM(quantity) AS total_out
    FROM stock_outs
    WHERE date BETWEEN :start AND :end
    GROUP BY item_id
) so ON so.item_id = i.id
ORDER BY i.name;
```

## 10. Daftar Halaman dan Route

| Halaman | Route | Keterangan |
| --- | --- | --- |
| Login | /login |  |
| Dashboard | /dashboard | Ringkasan dan alert stok menipis |
| Barang | /items | Daftar, tambah, ubah, hapus |
| Pemasok | /suppliers | Daftar, tambah, ubah, hapus |
| Barang Masuk | /stock-ins | Riwayat dan form tambah |
| Barang Keluar | /stock-outs | Riwayat dan form tambah |
| Stok Opname | /stock-opnames | Riwayat dan form opname |
| Stok Menipis | /low-stock | Daftar lengkap barang menipis |
| Laporan Harian | /reports/daily |  |
| Laporan Bulanan | /reports/monthly |  |

## 11. Alur Pengguna Utama

1. **Menerima barang dari pemasok:** Login, buka Barang Masuk, klik Tambah, pilih barang dan pemasok, isi jumlah dan harga, simpan. Stok bertambah otomatis.
2. **Mengeluarkan barang:** Buka Barang Keluar, klik Tambah, pilih barang, isi jumlah dan alasan, simpan. Jika stok kurang, sistem menolak dengan pesan jelas.
3. **Opname akhir bulan:** Buka Stok Opname, pilih barang, isi stok fisik, periksa selisih, konfirmasi. Stok sistem disesuaikan.
4. **Memeriksa stok menipis:** Dashboard menampilkan daftar barang yang perlu dipesan ulang beserta stok dan batas minimumnya.
5. **Membuat laporan:** Buka Laporan, pilih tanggal atau bulan, lihat tabel, cetak atau ekspor.

## 12. Arsitektur dan Teknologi

| Lapisan | Teknologi |
| --- | --- |
| Frontend | HTML, CSS, JavaScript, Blade template Laravel |
| Backend | PHP 8.2+, Laravel 11 |
| Database | MySQL 8 |
| Autentikasi | Laravel Breeze (versi Blade) atau auth manual |
| Ekspor (opsional) | barryvdh/laravel-dompdf, maatwebsite/excel |
| Versi kode | Git dan GitHub (branch per fitur) |

Pola MVC: Model (`Item`, `Supplier`, `StockIn`, `StockOut`, `StockOpname`), Controller per modul, dan view Blade dengan satu layout utama.

## 13. Kriteria Penerimaan

- Barang masuk 10 unit menambah stok barang tepat 10 unit.
- Barang keluar melebihi stok ditolak dan stok tidak berubah.
- Opname dengan stok fisik berbeda mengubah stok sistem dan menyimpan selisih.
- Barang dengan stok kurang dari atau sama dengan batas minimum muncul di daftar stok menipis.
- Laporan bulanan: stok awal + masuk − keluar = stok akhir untuk setiap barang.
- Barang atau pemasok yang sudah punya transaksi tidak dapat dihapus.
- Halaman utama tidak dapat dibuka tanpa login.
- Database dapat dibuat ulang dengan `php artisan migrate --seed`.

## 14. Pembagian Tugas (5 Anggota)

| Anggota | Peran | Tanggung jawab |
| --- | --- | --- |
| 1 | Ketua / Database | ERD, migration, seeder, query laporan, optimasi index |
| 2 | Backend A | Autentikasi, CRUD Barang dan Pemasok |
| 3 | Backend B | Barang Masuk, Barang Keluar, logika transaksi stok |
| 4 | Backend C / Fitur | Stok Opname, alert stok menipis, laporan harian dan bulanan |
| 5 | Frontend / QA | Layout, komponen UI sesuai DESIGN.md, dashboard, pengujian, dokumentasi |

Semua anggota ikut menulis laporan dan menyiapkan presentasi bagian masing-masing.

## 15. Jadwal (Contoh 6 Minggu)

| Minggu | Target |
| --- | --- |
| 1 | Analisis kebutuhan, ERD, setup Laravel dan repository, layout dasar |
| 2 | Migration, seeder, autentikasi, CRUD Barang dan Pemasok |
| 3 | Barang Masuk dan Barang Keluar beserta logika stok |
| 4 | Stok Opname, alert stok menipis, dashboard |
| 5 | Laporan harian dan bulanan, cetak atau ekspor, perapihan UI |
| 6 | Pengujian, perbaikan bug, dokumentasi, laporan akhir, presentasi |

## 16. Risiko dan Mitigasi

| Risiko | Mitigasi |
| --- | --- |
| Stok tidak konsisten karena akses bersamaan | `DB::transaction` dan `lockForUpdate()` |
| Logika laporan harian rumit | Sepakati rumus stok awal di minggu 1 dan tulis di dokumentasi |
| Konflik kode antar anggota | Branch per fitur, pull request, dan review sebelum merge |
| Waktu terbatas | Kerjakan seluruh P0 dahulu; P1 dan P2 hanya jika waktu cukup |
| Data uji kurang | Siapkan seeder berisi 30–50 barang dan transaksi contoh |

## 17. Deliverable Proyek

- Source code di repository Git.
- File SQL atau migration dan seeder.
- ERD dan kamus data.
- Dokumen PRD ini dan `DESIGN.md`.
- Laporan proyek dan slide presentasi.
- Demo aplikasi yang berjalan.

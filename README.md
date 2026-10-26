# InventarisKu

> Sistem Manajemen Inventaris Barang berbasis web untuk membantu gudang
> atau toko mengelola data barang, pemasok, pergerakan stok, stok
> opname, pemantauan stok menipis, dan laporan.

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0.16%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-000000.svg)](LICENSE)

------------------------------------------------------------------------

## Daftar Isi

-   [Tentang Proyek](#tentang-proyek)
-   [Tujuan](#tujuan)
-   [Fitur](#fitur)
-   [Peran Pengguna](#peran-pengguna)
-   [Teknologi](#teknologi)
-   [Arsitektur Singkat](#arsitektur-singkat)
-   [Struktur Repository](#struktur-repository)
-   [Prasyarat](#prasyarat)
-   [Instalasi dan Setup](#instalasi-dan-setup)
-   [Akun Demo](#akun-demo)
-   [Alur Kerja Utama](#alur-kerja-utama)
-   [Aturan Bisnis Penting](#aturan-bisnis-penting)
-   [Pengujian Manual](#pengujian-manual)
-   [Dokumentasi](#dokumentasi)
-   [Workflow Git untuk Tim](#workflow-git-untuk-tim)
-   [Troubleshooting](#troubleshooting)
-   [Roadmap](#roadmap)
-   [Kontribusi](#kontribusi)
-   [Lisensi](#lisensi)

## Tentang Proyek

InventarisKu adalah aplikasi web manajemen inventaris yang dikembangkan
sebagai proyek kelompok Mata Kuliah Sistem Basis Data.

Aplikasi berfokus pada pengelolaan siklus stok:

**Barang → Barang Masuk → Barang Keluar → Stok Opname → Laporan**

Sistem menggunakan Laravel dan MySQL untuk menjaga integritas data serta
menyediakan antarmuka yang sederhana untuk operasional gudang atau toko.

## Tujuan

1.  Memusatkan data barang dan pemasok dalam satu sistem.
2.  Mencatat barang masuk dan barang keluar secara terstruktur.
3.  Menjaga konsistensi stok melalui transaksi database.
4.  Membantu pengguna menemukan barang yang perlu segera diisi ulang.
5.  Menyediakan laporan stok harian dan bulanan.
6.  Menyediakan fondasi database yang dapat dibuat ulang melalui
    migration dan seeder.

## Peran Pengguna

### Admin

Admin memiliki akses operasional penuh sesuai aturan aplikasi, termasuk
aksi destruktif:

-   Mengelola data barang dan pemasok.
-   Mencatat barang masuk dan keluar.
-   Melakukan stok opname.
-   Melihat laporan.
-   Menghapus barang/pemasok yang belum memiliki transaksi.
-   Membatalkan transaksi barang masuk/keluar.

### Staf Gudang

Staf dapat menjalankan operasi inventaris sehari-hari:

-   Melihat dan mengelola data operasional yang tersedia.
-   Mencatat barang masuk dan barang keluar.
-   Melakukan stok opname.
-   Melihat stok menipis.
-   Melihat laporan.

Aksi destruktif tertentu seperti penghapusan data dan pembatalan
transaksi dibatasi untuk Admin.

## Teknologi

  Komponen          Teknologi
  ----------------- --------------------------------
  Bahasa backend    PHP 8.3+
  Framework         Laravel 13.x
  Database          MySQL 8.0.16+
  ORM               Laravel Eloquent
  Template          Blade
  Frontend          HTML, CSS, JavaScript
  UI font           Poppins
  Build tooling     Vite + Tailwind CSS dependency
  Authentication    Laravel Auth
  Version control   Git + GitHub

### Catatan frontend

Versi aplikasi saat ini memuat stylesheet dan JavaScript aplikasi secara
langsung dari `public/css` dan `public/js`. Dependensi Vite/Tailwind
tetap tersedia di `package.json` untuk kebutuhan pengembangan lebih
lanjut.

## Arsitektur Singkat

Aplikasi mengikuti pola MVC Laravel:

``` text
Browser
   │
   ▼
Routes (routes/web.php)
   │
   ▼
Controllers
   │
   ├── Validation
   │
   ├── Business Logic
   │
   ▼
Models / Eloquent
   │
   ▼
MySQL
```

Logika perubahan stok dipusatkan di:

``` text
app/Services/StockService.php
```

Tujuannya agar perubahan `items.stock` tidak dilakukan secara langsung
dari berbagai controller.

Setiap operasi stok penting menggunakan:

-   `DB::transaction()`
-   `lockForUpdate()`
-   validasi jumlah
-   pemeriksaan stok sebelum barang keluar

## Struktur Repository

``` text
inventaris/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Providers/
│   └── Services/
│       └── StockService.php
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── docs/
│   ├── DESIGN.md
│   ├── ERD.mermaid
│   ├── PRD.md
│   └── CONTRIBUTING.md
├── lang/id/
├── public/
│   ├── css/
│   └── js/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   └── web.php
├── .env.example
├── artisan
├── composer.json
├── package.json
└── README.md
```

## Prasyarat

Pastikan perangkat pengembang memiliki:

-   PHP **8.3 atau lebih baru**
-   Composer
-   MySQL **8.0.16 atau lebih baru**
-   Git
-   Browser modern: Chrome, Edge, atau Firefox

XAMPP dapat digunakan sebagai lingkungan lokal untuk Apache dan MySQL.

## Instalasi dan Setup

### 1. Clone repository

``` bash
git clone https://github.com/aditnurdiansyah/inventaris.git
cd inventaris
```

### 2. Install dependency PHP

``` bash
composer install
```

### 3. Buat file environment

Windows CMD:

``` cmd
copy .env.example .env
```

macOS/Linux:

``` bash
cp .env.example .env
```

### 4. Generate application key

``` bash
php artisan key:generate
```

Jangan membuat atau menyalin `APP_KEY` dari anggota tim lain. Setiap
environment harus memiliki key sendiri.

### 5. Konfigurasi database

Buat database kosong bernama:

``` text
inventaris
```

Kemudian sesuaikan `.env`:

``` dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventaris
DB_USERNAME=root
DB_PASSWORD=
```

`DB_PASSWORD` harus diisi sesuai password MySQL lokal masing-masing
developer. Jangan commit `.env`.

### 6. Pastikan timezone

Project menggunakan timezone:

``` text
Asia/Jakarta
```

Konfigurasi ini penting agar tanggal transaksi dan laporan sesuai waktu
Indonesia.

### 7. Jalankan migration dan seeder

Untuk database development yang boleh di-reset:

``` bash
php artisan migrate:fresh --seed
```

> **Peringatan:** `migrate:fresh` menghapus seluruh tabel pada database
> yang dikonfigurasi. Jangan digunakan pada database produksi atau
> database yang berisi data penting.

Jika hanya ingin menjalankan migration baru:

``` bash
php artisan migrate
```

### 8. Bersihkan cache konfigurasi jika mengubah `.env`

``` bash
php artisan config:clear
```

### 9. Jalankan aplikasi

``` bash
php artisan serve
```

Buka:

``` text
http://127.0.0.1:8000
```

## Akun Demo

Seeder menyediakan akun development berikut:

  Role    Email                     Password
  ------- ------------------------- ------------
  Admin   `admin@inventaris.test`   `password`
  Staf    `staf@inventaris.test`    `password`

> Akun tersebut hanya untuk development/demo. Jangan menggunakan
> password default tersebut pada production.

Seeder juga menyediakan data contoh barang, pemasok, dan transaksi agar
fitur dashboard, stok menipis, serta laporan dapat langsung diuji.

## Alur Kerja Utama

### Barang Masuk

``` text
Login
  ↓
Barang Masuk
  ↓
Tambah transaksi
  ↓
Validasi
  ↓
Simpan transaksi + tambah stok
  ↓
Tampilkan stok terbaru
```

### Barang Keluar

``` text
Login
  ↓
Barang Keluar
  ↓
Pilih barang + jumlah
  ↓
Kunci baris barang
  ↓
Cek stok tersedia
  ├── Tidak cukup → Tolak transaksi
  └── Cukup → Simpan transaksi + kurangi stok
```

### Stok Opname

``` text
Pilih barang
  ↓
Tampilkan stok sistem
  ↓
Isi stok fisik
  ↓
Hitung selisih
  ↓
Konfirmasi
  ↓
Simpan riwayat opname
  ↓
Set stok = stok fisik
```

## Aturan Bisnis Penting

### 1. Stok tidak boleh negatif

Transaksi barang keluar ditolak jika:

``` text
jumlah keluar > stok tersedia
```

### 2. Semua perubahan stok melalui StockService

Jangan melakukan:

``` php
$item->update(['stock' => ...]);
```

secara sembarang dari controller baru.

Perubahan stok harus ditempatkan di `StockService` agar transaksi dan
locking tetap konsisten.

### 3. Stok awal hanya saat barang dibuat

Saat barang dibuat, `stock` dapat diisi sebagai stok awal.

Setelah barang dibuat, stok berubah melalui:

-   Barang masuk
-   Barang keluar
-   Stok opname

### 4. Status stok

``` text
stock > min_stock          → Aman
0 < stock <= min_stock     → Menipis
stock = 0                  → Habis
```

### 5. Data yang sudah memiliki transaksi tidak boleh dihapus

Barang tidak dapat dihapus jika sudah memiliki:

-   barang masuk,
-   barang keluar, atau
-   stok opname.

Pemasok tidak dapat dihapus jika sudah digunakan pada transaksi barang
masuk.

### 6. Integritas transaksi

Perubahan stok dilakukan dalam database transaction. Jika proses gagal,
perubahan terkait dibatalkan.

## Pengujian Manual

Sebelum Pull Request, minimal lakukan pemeriksaan berikut:

-   [ ] Login Admin berhasil.
-   [ ] Login Staf berhasil.
-   [ ] Pengguna tanpa login diarahkan ke `/login`.
-   [ ] Tambah barang berhasil.
-   [ ] Kode barang duplikat ditolak.
-   [ ] Barang masuk menambah stok sesuai jumlah.
-   [ ] Barang keluar mengurangi stok sesuai jumlah.
-   [ ] Barang keluar melebihi stok ditolak.
-   [ ] Stok opname mengubah stok sesuai stok fisik.
-   [ ] Selisih opname tersimpan.
-   [ ] Barang dengan stok `<= min_stock` muncul di Stok Menipis.
-   [ ] Barang/pemasok yang sudah memiliki transaksi tidak dapat
    dihapus.
-   [ ] Staf tidak dapat melakukan aksi Admin-only.
-   [ ] Laporan harian menampilkan periode yang benar.
-   [ ] Laporan bulanan menampilkan periode yang benar.
-   [ ] Tampilan desktop dan tablet tetap usable.
-   [ ] Tidak ada error baru di browser atau Laravel log.

## Dokumentasi

Dokumentasi utama repository:

  -----------------------------------------------------------------------
  Dokumen                             Fungsi
  ----------------------------------- -----------------------------------
  `README.md`                         Orientasi proyek, instalasi,
                                      penggunaan, dan informasi umum

  `docs/PRD.md`                       Kebutuhan produk, ruang lingkup,
                                      aturan bisnis, dan acceptance
                                      criteria

  `docs/DESIGN.md`                    Standar UI/UX dan aturan visual

  `docs/ERD.mermaid`                  Struktur dan relasi basis data

  -----------------------------------------------------------------------

Untuk aturan kontribusi tim, gunakan dokumen terpisah:

``` text
CONTRIBUTING.md
```

## Workflow Git untuk Tim

Gunakan branch terpisah untuk setiap pekerjaan:

``` text
main
 ├── feature/items-crud
 ├── feature/stock-in
 ├── feature/stock-out
 ├── feature/stock-opname
 └── fix/report-calculation
```

Alur yang disarankan:

``` text
main
  ↓
buat feature branch
  ↓
kerjakan fitur
  ↓
uji lokal
  ↓
commit
  ↓
push branch
  ↓
Pull Request
  ↓
Code Review
  ↓
Merge ke main
```

### Format commit yang disarankan

``` text
feat: tambah validasi stok barang keluar
fix: perbaiki perhitungan laporan bulanan
refactor: pindahkan logika stok ke StockService
docs: perbarui dokumentasi instalasi
style: rapikan tampilan tabel barang
test: tambah pengujian transaksi stok
```

Jangan commit:

``` text
.env
/vendor
/node_modules
/public/build
```

## Troubleshooting

### `SQLSTATE[HY000] [1045] Access denied`

Periksa:

``` dotenv
DB_USERNAME=root
DB_PASSWORD=...
```

Pastikan MySQL yang berjalan adalah server yang sama dengan yang
dikonfigurasi Laravel.

Setelah mengubah `.env`:

``` bash
php artisan config:clear
```

### `Migration table not found`

Database belum memiliki migration. Jalankan:

``` bash
php artisan migrate
```

atau untuk database development yang boleh di-reset:

``` bash
php artisan migrate:fresh --seed
```

### `MissingAppKeyException`

Jalankan:

``` bash
php artisan key:generate
```

### Perubahan `.env` tidak terbaca

``` bash
php artisan config:clear
```

### Font tidak tampil sebagai Poppins

UI menggunakan Google Fonts. Pastikan perangkat memiliki koneksi
internet saat membuka aplikasi. Jika tidak, browser menggunakan fallback
font.

## Kontribusi

Kontribusi anggota tim mengikuti PRD, DESIGN.md, dan aturan kerja pada
`CONTRIBUTING.md`.

Sebelum membuat Pull Request:

1.  Pastikan branch berasal dari `main` terbaru.
2.  Kerjakan satu fitur/perbaikan yang jelas.
3.  Jalankan pengujian yang relevan.
4.  Pastikan tidak ada secret yang ikut ter-commit.
5.  Buat Pull Request dengan deskripsi perubahan dan cara pengujian.

## Lisensi

Project ini menggunakan lisensi MIT sesuai konfigurasi Laravel project.
Lihat file `LICENSE` pada repository untuk ketentuan lengkap.

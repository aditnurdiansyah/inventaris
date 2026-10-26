# InventarisKu: Sistem Manajemen Inventaris Barang

Aplikasi web manajemen stok gudang atau toko. Tugas mata kuliah Sistem Basis Data.
Stack: HTML, CSS, JavaScript, PHP, Laravel, MySQL.

Paket ini adalah **isi aplikasi** (controller, model, view, migration, seeder, CSS, JS), bukan seluruh framework Laravel. Cara pakainya: buat proyek Laravel baru, lalu timpa dengan isi paket ini. Tidak perlu Node.js atau `npm` karena CSS dan JS sudah siap di folder `public/`.

## Fitur

| Fitur | Halaman |
|---|---|
| Login dan logout, dua peran (admin, staf) | `/login` |
| Dashboard: ringkasan, stok menipis, transaksi terakhir | `/dashboard` |
| Data barang: cari, filter kategori, tambah, ubah, hapus | `/items` |
| Data pemasok | `/suppliers` |
| Barang masuk: stok bertambah otomatis, filter, pembatalan | `/stock-ins` |
| Barang keluar: tolak jika stok kurang, filter, pembatalan | `/stock-outs` |
| Stok opname: banyak barang sekaligus, selisih langsung terhitung | `/stock-opnames` |
| Alert stok menipis: badge di menu, daftar lengkap | `/low-stock` |
| Laporan stok harian dan bulanan, cetak | `/reports/daily`, `/reports/monthly` |

Hak akses: staf dapat mencatat transaksi, opname, dan melihat laporan. Hanya admin yang dapat menghapus barang atau pemasok dan membatalkan transaksi.

## Prasyarat

- PHP 8.2 atau lebih baru dan Composer
- MySQL 8.0.16 atau lebih baru (XAMPP versi terbaru sudah cukup). Versi lama atau MariaDB mengabaikan constraint `CHECK`, tetapi validasi di aplikasi tetap berjalan.

## Instalasi

**1. Buat proyek Laravel baru**

```
composer create-project laravel/laravel inventaris
cd inventaris
```

**2. Salin isi paket ini ke dalam folder `inventaris`**

Pilih "ganti/timpa" untuk berkas yang sudah ada (`routes/web.php`, `app/Models/User.php`, `DatabaseSeeder.php`).

**3. Atur `.env`**

```
APP_NAME="InventarisKu"
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventaris
DB_USERNAME=root
DB_PASSWORD=
```

**4. Atur zona waktu** di `config/app.php`. Ubah `'timezone' => 'UTC'` menjadi `'timezone' => 'Asia/Jakarta'`. Tanpa ini, tanggal "hari ini" bisa salah pada dini hari.

**5. Buat database kosong bernama `inventaris`** (lewat phpMyAdmin atau `CREATE DATABASE inventaris;`).

**6. Jalankan migrasi dan seeder**

```
php artisan migrate:fresh --seed
```

**7. Jalankan aplikasi**

```
php artisan serve
```

Buka http://127.0.0.1:8000 lalu masuk dengan:

| Peran | Email | Password |
|---|---|---|
| Admin | admin@inventaris.test | password |
| Staf | staf@inventaris.test | password |

Data contoh: 15 barang, 4 pemasok, serta transaksi masuk dan keluar. `BRG-001` sengaja menipis dan `BRG-002` sengaja habis agar alert langsung terlihat.

## Struktur Penting

```
app/Services/StockService.php        Seluruh perubahan stok (DB::transaction + lockForUpdate)
app/Http/Controllers/                Satu controller per modul
app/Models/                          Item, Supplier, StockIn, StockOut, StockOpname, User
database/migrations/                 6 migration (4 tabel inti + stock_opnames + role di users)
database/seeders/                    Data contoh yang konsisten dengan stok
resources/views/                     Blade: layouts, components, dan halaman per modul
public/css/app.css                   Gaya sesuai docs/DESIGN.md
public/js/app.js                     Modal konfirmasi, hitung selisih opname, filter tabel
lang/id/validation.php               Pesan validasi Bahasa Indonesia
docs/                                DESIGN.md dan ERD.mermaid
```

## Konsep yang Perlu Dipahami Tim

**Konsistensi stok.** `items.stock` adalah kolom turunan. Semua perubahan stok wajib lewat `StockService`, tidak boleh `update` langsung dari controller lain. Setiap method memakai `DB::transaction` (semua langkah berhasil atau semua dibatalkan) dan `lockForUpdate()` (baris barang dikunci agar dua pengguna tidak saling menimpa).

**Rumus laporan.** Dihitung mundur dari stok saat ini, sehingga tetap benar walau barang dibuat dengan stok awal:

```
mutasi(periode) = masuk - keluar + penyesuaian opname
stok akhir      = stok saat ini - mutasi(setelah periode)
stok awal       = stok akhir - mutasi(periode)
```

Karena opname ikut memengaruhi stok, laporan memiliki kolom tambahan "Penyesuaian Opname". Dengan kolom itu persamaan `stok awal + masuk - keluar + penyesuaian = stok akhir` selalu terpenuhi. Ini sedikit memperluas kriteria di PRD bagian 13.

**Status stok.** Aman jika `stok > minimum`, Menipis jika `0 < stok <= minimum`, Habis jika `stok = 0`.

## Daftar Uji (Kriteria Penerimaan PRD)

1. Barang masuk 10 unit: stok barang bertambah tepat 10, dan pesan sukses menampilkan stok terbaru.
2. Barang keluar melebihi stok: ditolak dengan pesan di bawah kolom jumlah, stok tidak berubah.
3. Opname dengan stok fisik berbeda: stok sistem berubah, selisih tersimpan di riwayat.
4. Barang dengan stok sama dengan atau di bawah minimum muncul di `/low-stock` dan di badge menu.
5. Laporan bulanan: untuk setiap barang, stok awal + masuk - keluar + penyesuaian = stok akhir.
6. Hapus barang atau pemasok yang punya transaksi: ditolak dengan pesan jelas.
7. Buka `/items` tanpa login: dialihkan ke halaman login.
8. Login sebagai staf: tombol Hapus dan Batalkan tidak tampil, dan akses langsung ditolak (403).
9. `php artisan migrate:fresh --seed` membangun ulang database dari nol.

Query untuk memeriksa konsistensi stok (hasil kosong berarti konsisten, hanya berlaku jika belum ada opname atau stok awal manual):

```sql
SELECT i.code, i.stock,
       COALESCE((SELECT SUM(quantity) FROM stock_ins  WHERE item_id = i.id), 0)
     - COALESCE((SELECT SUM(quantity) FROM stock_outs WHERE item_id = i.id), 0) AS hitung_ulang
FROM items i
HAVING i.stock <> hitung_ulang;
```

## Pemecahan Masalah

| Masalah | Penyebab dan solusi |
|---|---|
| `SQLSTATE ... Unknown database` | Database `inventaris` belum dibuat (langkah 5) |
| `Access denied for user` | Cek `DB_USERNAME` dan `DB_PASSWORD` di `.env` |
| Halaman tanpa gaya | Pastikan `public/css/app.css` ikut tersalin, lalu `php artisan view:clear` |
| Pesan validasi berbahasa Inggris | `APP_LOCALE=id` belum diatur, lalu `php artisan config:clear` |
| Error pada `CHECK` saat migrate | MySQL di bawah 8.0.16. Perbarui MySQL atau hapus baris `DB::statement` di migration |
| Tampilan kecil dan kosong di sisi kanan | Zoom browser di bawah 100% atau skala layar. Tekan Ctrl+0 untuk mengembalikan zoom, lalu muat ulang dengan Ctrl+F5 |
| Font bukan Poppins | Font dimuat dari Google Fonts, jadi perlu internet. Tanpa internet tampil font sistem |
| Perubahan `.env` tidak terbaca | Jalankan `php artisan config:clear` |

## Pengembangan Lanjutan (Opsional)

- Ekspor PDF atau Excel (`barryvdh/laravel-dompdf`, `maatwebsite/excel`)
- Halaman kelola pengguna untuk admin (FR-04)
- Grafik pergerakan masuk dan keluar di dashboard (FR-74)
- Pengujian otomatis `StockService` dengan PHPUnit

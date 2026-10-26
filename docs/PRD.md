# Product Requirements Document (PRD)

## 1. Tujuan Dokumen

PRD ini menjadi **sumber acuan produk** bagi seluruh anggota tim selama
pengembangan InventarisKu.

PRD menjawab pertanyaan:

> **Apa yang harus dibangun, untuk siapa, mengapa, dan seperti apa hasil
> yang dianggap benar?**

PRD tidak mengatur detail workflow Git, format commit, proses Pull
Request, atau pembagian prosedur kerja teknis. Hal tersebut ditempatkan
pada `CONTRIBUTING.md`.

------------------------------------------------------------------------

## 2. Ringkasan Produk

InventarisKu adalah aplikasi web untuk membantu gudang atau toko
mengelola data inventaris secara terpusat.

Sistem menggantikan pencatatan stok yang sebelumnya dapat dilakukan
secara manual melalui buku atau spreadsheet dengan proses digital yang
lebih terstruktur.

Ruang lingkup inti aplikasi:

1.  Autentikasi pengguna.
2.  Pengelolaan barang.
3.  Pengelolaan pemasok.
4.  Pencatatan barang masuk.
5.  Pencatatan barang keluar.
6.  Stok opname.
7.  Pemantauan stok menipis.
8.  Laporan stok harian dan bulanan.
9.  Dashboard ringkasan operasional.

Konsep utama sistem:

``` text
Data Barang
     │
     ├── Barang Masuk ──► Stok bertambah
     │
     ├── Barang Keluar ─► Stok berkurang
     │
     └── Stok Opname ───► Stok disesuaikan
                              │
                              ▼
                         Laporan Stok
```

------------------------------------------------------------------------

## 3. Latar Belakang dan Masalah

Pengelolaan inventaris secara manual memiliki beberapa masalah:

-   stok mudah tidak sesuai dengan kondisi fisik;
-   pencatatan barang masuk dan keluar tidak terpusat;
-   sulit mengetahui barang yang perlu segera diisi ulang;
-   riwayat transaksi sulit ditelusuri;
-   pembuatan laporan membutuhkan perhitungan manual;
-   perubahan stok dapat menimbulkan kesalahan jika dilakukan tanpa
    aturan yang konsisten.

InventarisKu dikembangkan untuk menyediakan satu sumber data inventaris
dengan aturan perubahan stok yang terkontrol.

------------------------------------------------------------------------

## 4. Tujuan Produk

### 4.1 Tujuan Utama

Menyediakan sistem inventaris berbasis web yang sederhana, konsisten,
dan dapat menjaga integritas data stok.

### 4.2 Tujuan Spesifik

-   Memusatkan data barang dan pemasok.
-   Mengotomatisasi perubahan stok akibat transaksi.
-   Menolak transaksi barang keluar yang melebihi stok.
-   Menyediakan proses stok opname.
-   Menampilkan peringatan stok menipis.
-   Menyediakan laporan berdasarkan periode.
-   Menyediakan migration dan seeder agar database dapat direproduksi.
-   Menyediakan struktur kode yang mudah dipelihara oleh tim.

------------------------------------------------------------------------

## 5. Sasaran Pengguna

### 5.1 Admin

Admin bertanggung jawab terhadap pengelolaan operasional dan aksi yang
berdampak destruktif terhadap data.

Kebutuhan utama:

-   mengelola data barang dan pemasok;
-   mengelola transaksi stok;
-   melakukan stok opname;
-   memantau stok;
-   melihat laporan;
-   membatalkan transaksi;
-   menghapus data yang memenuhi aturan penghapusan.

### 5.2 Staf Gudang

Staf menggunakan aplikasi untuk kegiatan operasional harian.

Kebutuhan utama:

-   mencatat barang masuk;
-   mencatat barang keluar;
-   melakukan stok opname;
-   memantau stok;
-   melihat laporan;
-   mengelola data operasional yang diizinkan sistem.

------------------------------------------------------------------------

## 6. Modul Produk

  Modul           Tujuan
  --------------- ------------------------------------------------
  Autentikasi     Mengontrol akses ke aplikasi
  Dashboard       Memberikan ringkasan kondisi inventaris
  Barang          Mengelola master barang
  Pemasok         Mengelola master pemasok
  Barang Masuk    Menambah stok melalui transaksi
  Barang Keluar   Mengurangi stok melalui transaksi
  Stok Opname     Menyesuaikan stok sistem dengan stok fisik
  Stok Menipis    Menampilkan barang yang perlu diperhatikan
  Laporan         Menyediakan ringkasan stok berdasarkan periode

------------------------------------------------------------------------

## 7. Aturan Bisnis

### BR-01 - Stok tidak boleh negatif

``` text
stock >= 0
```

Sistem harus menolak transaksi yang membuat stok menjadi negatif.

### BR-02 - Barang keluar

``` text
jika quantity > stock:
    transaksi ditolak
jika quantity <= stock:
    transaksi disimpan
    stock = stock - quantity
```

### BR-03 - Barang masuk

``` text
transaksi disimpan
stock = stock + quantity
```

### BR-04 - Stok opname

``` text
difference = physical_stock - system_stock
stock baru = physical_stock
```

Riwayat opname tetap menyimpan stok sistem sebelum penyesuaian.

### BR-05 - Status stok

``` text
stock = 0                 → Habis
0 < stock <= min_stock    → Menipis
stock > min_stock         → Aman
```

### BR-06 - Penghapusan barang

Barang hanya dapat dihapus jika belum memiliki transaksi:

-   `stock_ins`;
-   `stock_outs`;
-   `stock_opnames`.

### BR-07 - Penghapusan pemasok

Pemasok hanya dapat dihapus jika belum digunakan pada `stock_ins`.

### BR-08 - Aksi Admin

Aksi berikut hanya dapat dilakukan Admin:

-   menghapus barang;
-   menghapus pemasok;
-   membatalkan barang masuk;
-   membatalkan barang keluar.

### BR-09 - Perubahan stok terpusat

Perubahan stok harus menggunakan `App\Services\StockService`.

Controller tidak boleh memiliki implementasi stok yang terpisah-pisah.

------------------------------------------------------------------------

## 8. Model Data

### 8.1 Entitas

``` text
users
items
suppliers
stock_ins
stock_outs
stock_opnames
```

### 8.2 Relasi

``` text
users      1 ─── N stock_ins
users      1 ─── N stock_outs
users      1 ─── N stock_opnames

suppliers  1 ─── N stock_ins

items      1 ─── N stock_ins
items      1 ─── N stock_outs
items      1 ─── N stock_opnames
```

ERD lengkap tersedia pada:

``` text
docs/ERD.mermaid
```

### 8.3 `items`

  Field        Tipe            Aturan
  ------------ --------------- ---------------
  id           BIGINT          PK
  code         VARCHAR(30)     unique, wajib
  name         VARCHAR(150)    wajib
  category     VARCHAR(80)     wajib
  unit         VARCHAR(20)     wajib
  buy_price    DECIMAL(12,2)   \>= 0
  sell_price   DECIMAL(12,2)   \>= 0
  stock        INT             \>= 0
  min_stock    INT             \>= 0
  created_at   TIMESTAMP       Laravel
  updated_at   TIMESTAMP       Laravel

### 8.4 `suppliers`

  Field        Tipe           Aturan
  ------------ -------------- ----------
  id           BIGINT         PK
  name         VARCHAR(150)   wajib
  phone        VARCHAR(20)    nullable
  email        VARCHAR(100)   nullable
  address      TEXT           nullable
  created_at   TIMESTAMP      Laravel
  updated_at   TIMESTAMP      Laravel

### 8.5 `stock_ins`

  Field         Tipe            Aturan
  ------------- --------------- --------------
  id            BIGINT          PK
  item_id       BIGINT          FK
  supplier_id   BIGINT          FK
  user_id       BIGINT          FK
  date          DATE            \<= hari ini
  quantity      INT             \> 0
  buy_price     DECIMAL(12,2)   \>= 0
  note          VARCHAR(255)    nullable

### 8.6 `stock_outs`

  Field      Tipe           Aturan
  ---------- -------------- -----------------------------------
  id         BIGINT         PK
  item_id    BIGINT         FK
  user_id    BIGINT         FK
  date       DATE           \<= hari ini
  quantity   INT            \> 0
  reason     ENUM           terjual/rusak/kedaluwarsa/lainnya
  note       VARCHAR(255)   nullable

### 8.7 `stock_opnames`

  Field            Tipe           Aturan
  ---------------- -------------- ----------------
  id               BIGINT         PK
  item_id          BIGINT         FK
  user_id          BIGINT         FK
  date             DATE           \<= hari ini
  system_stock     INT            stok sistem
  physical_stock   INT            \>= 0
  difference       INT            fisik - sistem
  note             VARCHAR(255)   nullable

------------------------------------------------------------------------

## 9. Logika Laporan

Sistem menggunakan pendekatan perhitungan mundur dari stok saat ini.

Untuk periode laporan:

``` text
mutasi periode =
    barang masuk
    - barang keluar
    + penyesuaian opname
```

Stok akhir periode dihitung dari stok saat ini dengan mengurangi mutasi
setelah periode:

``` text
stok akhir =
    stok saat ini
    - mutasi setelah periode
```

Kemudian:

``` text
stok awal =
    stok akhir
    - mutasi periode
```

Validasi utama:

``` text
stok awal
+ masuk
- keluar
+ penyesuaian opname
= stok akhir
```

Implementasi laporan saat ini menggunakan `ReportController`.

------------------------------------------------------------------------

## 10. Halaman dan Route

  Halaman           Route                Prioritas
  ----------------- -------------------- -----------
  Login             `/login`             P0
  Dashboard         `/dashboard`         P0
  Barang            `/items`             P0
  Pemasok           `/suppliers`         P0
  Barang Masuk      `/stock-ins`         P0
  Barang Keluar     `/stock-outs`        P0
  Stok Opname       `/stock-opnames`     P0
  Stok Menipis      `/low-stock`         P0
  Laporan Harian    `/reports/daily`     P0
  Laporan Bulanan   `/reports/monthly`   P0

------------------------------------------------------------------------

## 11. UX dan UI

Dokumen visual utama:

``` text
docs/DESIGN.md
```

Prinsip wajib:

-   sederhana;
-   whitespace cukup;
-   tidak menggunakan gradient;
-   tidak menggunakan glassmorphism;
-   tidak menggunakan shadow berlebihan;
-   menggunakan Bahasa Indonesia;
-   warna primer `#2563EB`;
-   teks utama `#111111`;
-   teks sekunder `#6B7280`;
-   border `#E5E7EB`;
-   font utama Poppins;
-   status selalu memiliki label teks, bukan hanya warna.

### Responsiveness

  Lebar         Perilaku
  ------------- -----------------------------------------------------
  \>= 1024px    Sidebar penuh
  768--1023px   Sidebar dapat dibuka/tutup
  \< 768px      Layout satu kolom, tabel dapat di-scroll horizontal

------------------------------------------------------------------------

## 12. Acceptance Criteria

Fitur baseline dianggap memenuhi PRD jika:

### Autentikasi

-   [ ] Pengguna dapat login dengan kredensial valid.
-   [ ] Kredensial invalid ditolak.
-   [ ] Pengguna anonim tidak dapat mengakses dashboard.
-   [ ] Logout mengakhiri session.

### Barang

-   [ ] Barang dapat ditambahkan dengan stok awal.
-   [ ] Kode barang harus unik.
-   [ ] Barang dapat dicari dan difilter.
-   [ ] Barang yang memiliki transaksi tidak dapat dihapus.
-   [ ] Stok tidak dapat diedit langsung setelah barang dibuat.

### Pemasok

-   [ ] Pemasok dapat ditambah dan diubah.
-   [ ] Pemasok dapat dicari.
-   [ ] Pemasok yang memiliki transaksi tidak dapat dihapus.

### Barang masuk

-   [ ] Transaksi valid menambah stok sesuai jumlah.
-   [ ] Jumlah 0 atau negatif ditolak.
-   [ ] Admin dapat membatalkan transaksi.
-   [ ] Pembatalan tidak menghasilkan stok negatif.

### Barang keluar

-   [ ] Transaksi valid mengurangi stok sesuai jumlah.
-   [ ] Jumlah melebihi stok ditolak.
-   [ ] Stok tidak berubah ketika transaksi ditolak.
-   [ ] Admin dapat membatalkan transaksi.

### Stok opname

-   [ ] Sistem menampilkan stok sistem.
-   [ ] Pengguna dapat memasukkan stok fisik.
-   [ ] Selisih dihitung dengan rumus fisik - sistem.
-   [ ] Stok berubah menjadi stok fisik setelah konfirmasi.
-   [ ] Riwayat opname tersimpan.

### Stok menipis

-   [ ] `stock <= min_stock` muncul pada daftar.
-   [ ] Stok 0 diberi status Habis.
-   [ ] Dashboard menampilkan barang menipis.

### Laporan

-   [ ] Laporan harian dapat memilih tanggal.
-   [ ] Laporan bulanan dapat memilih bulan dan tahun.
-   [ ] Laporan menampilkan stok awal, masuk, keluar, penyesuaian, dan
    stok akhir.
-   [ ] Persamaan stok tetap konsisten.

### Database

-   [ ] `php artisan migrate:fresh --seed` dapat membangun database
    development dari awal.
-   [ ] Seeder menghasilkan akun demo dan data contoh.
-   [ ] Foreign key dan aturan integritas berjalan sesuai migration.

------------------------------------------------------------------------

## 13. Data Seed Development

Seeder development menyediakan:

### Admin

``` text
Email    : admin@inventaris.test
Password : password
Role     : admin
```

### Staf

``` text
Email    : staf@inventaris.test
Password : password
Role     : staff
```

Kredensial ini hanya untuk development/demo.

------------------------------------------------------------------------

## 14. Arsitektur Teknis

### Backend

``` text
routes/web.php
      │
      ▼
Controllers
      │
      ├── validation
      │
      ▼
StockService (untuk perubahan stok)
      │
      ▼
Eloquent Models
      │
      ▼
MySQL
```

### Prinsip implementasi

1.  Controller mengatur HTTP request/response.
2.  Model merepresentasikan data dan relasi.
3.  `StockService` mengelola perubahan stok.
4.  Migration menjadi sumber struktur database.
5.  Seeder menjadi sumber data development.
6.  Blade menangani presentation layer.
7.  Validasi dilakukan di server.

------------------------------------------------------------------------

## 15. Definition of Done

Sebuah fitur dianggap selesai jika:

-   [ ] Kebutuhan fitur sudah sesuai PRD.
-   [ ] Implementasi mengikuti arsitektur Laravel project.
-   [ ] Validasi input tersedia.
-   [ ] Aturan bisnis terkait sudah diterapkan.
-   [ ] UI mengikuti `docs/DESIGN.md`.
-   [ ] Tidak ada secret yang di-commit.
-   [ ] Pengujian manual fitur berhasil.
-   [ ] Automated test dibuat jika fitur memiliki business logic kritis.
-   [ ] Dokumentasi diperbarui jika ada perubahan behavior.
-   [ ] Pull Request telah direview minimal oleh anggota tim lain.
-   [ ] Tidak ada perubahan langsung ke `main` tanpa review.

------------------------------------------------------------------------

## 16. Dokumentasi yang Berkaitan

  Dokumen              Fokus
  -------------------- ----------------------------------------------
  `README.md`          Setup, penggunaan, struktur, troubleshooting
  `docs/PRD.md`        Requirement produk dan acceptance criteria
  `docs/DESIGN.md`     UI/UX dan design system
  `docs/ERD.mermaid`   ERD dan relasi database
  `CONTRIBUTING.md`    Workflow Git, branch, commit, PR, review

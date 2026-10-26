# DESIGN.md: Sistem Manajemen Inventaris Barang

Panduan desain antarmuka untuk seluruh anggota tim. Semua halaman wajib mengikuti aturan di sini agar tampilan konsisten.

## 1. Prinsip Desain

1. **Sederhana.** Satu halaman, satu tujuan. Hapus elemen yang tidak membantu pengguna menyelesaikan tugas.
2. **Lega.** Ruang kosong adalah bagian dari desain, bukan sisa. Beri jarak yang cukup antar elemen.
3. **Jelas.** Teks hitam di atas putih, satu warna aksen biru. Warna dipakai untuk makna, bukan hiasan.
4. **Konsisten.** Komponen yang sama selalu terlihat dan bekerja sama di semua halaman.
5. **Tanpa efek berlebihan.** Tidak ada gradient, tidak ada glow, tidak ada glassmorphism, tidak ada bayangan tebal, tidak ada ilustrasi dekoratif, tidak ada emoji di antarmuka.

## 2. Warna

### 2.1 Palet Inti

| Token | Hex | Penggunaan |
|---|---|---|
| `--color-bg` | `#FFFFFF` | Latar halaman, kartu, tabel, modal |
| `--color-text` | `#111111` | Teks utama, judul, isi tabel |
| `--color-text-secondary` | `#6B7280` | Teks pendukung, label, placeholder, keterangan |
| `--color-primary` | `#2563EB` | Tombol utama, link, menu aktif, fokus input |
| `--color-primary-hover` | `#1D4ED8` | Hover tombol utama |
| `--color-border` | `#E5E7EB` | Garis tabel, border kartu dan input |
| `--color-surface` | `#F9FAFB` | Header tabel, baris hover, area sekunder |

### 2.2 Warna Status

Dipakai hanya untuk menyatakan kondisi stok dan pesan sistem. Gunakan teks berwarna dengan latar tipis, bukan blok warna pekat.

| Status | Teks | Latar | Contoh pemakaian |
|---|---|---|---|
| Berhasil | `#15803D` | `#F0FDF4` | Notifikasi simpan berhasil, stok aman |
| Peringatan | `#B45309` | `#FFFBEB` | Stok menipis |
| Bahaya | `#B91C1C` | `#FEF2F2` | Stok habis, error validasi, tombol hapus |

### 2.3 Aturan Warna

- Latar halaman selalu putih. Tidak ada mode gelap pada versi 1.
- Biru `#2563EB` hanya untuk elemen yang dapat diklik atau sedang aktif.
- Dilarang memakai gradient, dalam bentuk apa pun.
- Abu-abu sekunder tidak boleh dipakai untuk informasi penting (angka stok, nama barang).
- Rasio kontras teks terhadap latar minimal 4.5:1.

## 3. Tipografi

### 3.1 Font

**Poppins** (utama). Muat dari Google Fonts, bobot 400, 500, dan 600.

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
```

```css
font-family: 'Poppins', -apple-system, 'Segoe UI', Arial, sans-serif;
```

Montserrat boleh dipakai sebagai pengganti, tetapi pilih salah satu dan jangan dicampur.

### 3.2 Skala Ukuran

| Gaya | Ukuran | Bobot | Tinggi baris | Warna | Pemakaian |
|---|---|---|---|---|---|
| Judul halaman | 24px | 600 | 32px | `--color-text` | Satu per halaman |
| Judul bagian | 18px | 600 | 28px | `--color-text` | Judul kartu atau seksi |
| Isi | 14px | 400 | 22px | `--color-text` | Teks umum, isi tabel |
| Label | 14px | 500 | 20px | `--color-text` | Label form, header tabel |
| Teks pendukung | 13px | 400 | 20px | `--color-text-secondary` | Deskripsi, hint, tanggal |
| Angka ringkasan | 28px | 600 | 36px | `--color-text` | Angka di kartu dashboard |

### 3.3 Aturan Tipografi

- Rata kiri untuk semua teks. Hanya angka pada kolom tabel yang rata kanan.
- Tidak memakai huruf kapital semua (all caps) kecuali singkatan.
- Tidak memakai garis bawah kecuali pada link dalam paragraf.
- Maksimal dua bobot per layar (misalnya 400 dan 600).
- Panjang baris teks paragraf maksimal sekitar 70 karakter.

## 4. Spasi dan Layout

### 4.1 Skala Spasi

Seluruh jarak memakai kelipatan 4px.

| Token | Nilai | Pemakaian |
|---|---|---|
| `--space-1` | 4px | Jarak ikon ke teks |
| `--space-2` | 8px | Jarak label ke input |
| `--space-3` | 12px | Padding badge, jarak kecil |
| `--space-4` | 16px | Padding tombol, jarak antar field berdekatan |
| `--space-5` | 24px | Padding kartu, jarak antar field form |
| `--space-6` | 32px | Jarak antar kartu dan antar bagian |
| `--space-7` | 48px | Jarak antar seksi besar |
| `--space-8` | 64px | Padding atas halaman login |

### 4.2 Struktur Halaman

```
+----------+------------------------------------------------+
| Sidebar  |  Header halaman (judul + aksi utama)           |
| 240px    |------------------------------------------------|
|          |                                                |
| Logo     |  Konten                                        |
| Menu     |  (padding 32px, fluid, maks 1800px)             |
|          |                                                |
| Pengguna |                                                |
+----------+------------------------------------------------+
```

- **Sidebar:** lebar 240px, latar putih, border kanan 1px `--color-border`. Tetap di tempat saat scroll.
- **Area konten:** fluid (mengisi lebar layar), padding 32px (48px di layar 1440px ke atas), lebar maksimum 1800px dan diposisikan di tengah pada layar yang lebih lebar. Kartu form tetap maksimal 560px.
- **Header halaman:** judul di kiri, tombol aksi utama (misalnya "Tambah Barang") di kanan. Jarak ke konten 32px.
- **Grid:** 12 kolom, gap 24px.

### 4.3 Responsif

| Lebar layar | Perilaku |
|---|---|
| 1024px ke atas | Sidebar tampil penuh |
| 768px sampai 1023px | Sidebar menjadi menu yang dibuka dengan tombol, padding konten 24px |
| Di bawah 768px | Satu kolom, tabel dapat digeser horizontal, padding konten 16px |

## 5. Bentuk dan Efek

| Elemen | Aturan |
|---|---|
| Radius sudut | 6px untuk tombol, input, badge; 8px untuk kartu dan modal. Jangan memakai sudut pil (999px) kecuali badge status |
| Border | 1px solid `--color-border` |
| Bayangan | Tidak dipakai pada kartu. Hanya modal dan dropdown: `0 4px 12px rgba(0,0,0,0.08)` |
| Transisi | 150ms ease pada warna dan border saja. Tidak ada animasi gerak, pantulan, atau slide mencolok |
| Ikon | Gaya garis (outline), tebal 1.5px, ukuran 20px, warna mengikuti teks. Disarankan Lucide atau Heroicons Outline |

## 6. Komponen

### 6.1 Tombol

| Varian | Latar | Teks | Border | Pemakaian |
|---|---|---|---|---|
| Primer | `#2563EB` | Putih | Tidak ada | Satu aksi utama per layar (Simpan, Tambah) |
| Sekunder | Putih | `#111111` | 1px `--color-border` | Batal, Filter, Ekspor |
| Bahaya | Putih | `#B91C1C` | 1px `#FECACA` | Hapus |
| Teks | Transparan | `#2563EB` | Tidak ada | Aksi ringan seperti "Lihat semua" |

Ukuran: tinggi 40px, padding horizontal 16px, teks 14px bobot 500, radius 6px.
Status: hover primer menjadi `#1D4ED8`; hover sekunder berlatar `#F9FAFB`; disabled opacity 50% dan kursor `not-allowed`.
Fokus: outline 2px `#2563EB` dengan offset 2px.

### 6.2 Input Form

- Label di atas input, 14px bobot 500, jarak ke input 8px.
- Input: tinggi 40px, padding 12px, border 1px `--color-border`, radius 6px, latar putih.
- Placeholder: `--color-text-secondary`.
- Fokus: border `#2563EB` dan ring 3px `rgba(37,99,235,0.15)`.
- Error: border `#B91C1C`, pesan error 13px berwarna `#B91C1C` di bawah input dengan jarak 4px.
- Teks bantuan 13px abu-abu di bawah input.
- Jarak antar field: 24px.
- Wajib diisi ditandai dengan tanda bintang merah kecil setelah label.
- Form maksimal lebar 560px; tombol Simpan dan Batal berada di bawah, rata kiri.

### 6.3 Tabel

- Latar putih, border luar 1px `--color-border`, radius 8px.
- Header: latar `#F9FAFB`, teks 14px bobot 500, tinggi 44px.
- Baris: tinggi 52px, garis pemisah 1px `--color-border`, tanpa zebra stripe.
- Hover baris: latar `#F9FAFB`.
- Padding sel: 16px horizontal.
- Kolom angka (stok, harga, jumlah) rata kanan; kolom aksi rata kanan.
- Aksi per baris berupa teks link ("Ubah", "Hapus"), bukan deretan tombol berwarna.
- Pagination di bawah tabel, rata kanan, 10 data per halaman.
- Keadaan kosong: teks abu-abu di tengah tabel, misalnya "Belum ada data barang", disertai tombol primer jika relevan.

### 6.4 Kartu

- Latar putih, border 1px `--color-border`, radius 8px, padding 24px, tanpa bayangan.
- Kartu ringkasan dashboard: label 13px abu-abu di atas, angka 28px bobot 600 di bawah. Tanpa ikon berwarna besar.
- Jarak antar kartu 24px.

### 6.5 Badge Status

Tinggi 24px, padding horizontal 10px, teks 12px bobot 500, radius 999px, latar dan teks sesuai tabel status di bagian 2.2.

| Kondisi | Label | Warna |
|---|---|---|
| `stock > min_stock` | Aman | Berhasil |
| `0 < stock <= min_stock` | Menipis | Peringatan |
| `stock = 0` | Habis | Bahaya |

### 6.6 Navigasi Sidebar

- Latar putih. Logo atau nama aplikasi di atas dengan padding 24px.
- Item menu: tinggi 40px, padding horizontal 12px, ikon 20px + teks 14px, radius 6px, jarak antar item 4px.
- Item aktif: latar `#EFF6FF`, teks dan ikon `#2563EB`, bobot 500.
- Hover: latar `#F9FAFB`.
- Urutan menu: Dashboard, Barang, Pemasok, Barang Masuk, Barang Keluar, Stok Opname, Stok Menipis (dengan badge jumlah), Laporan.
- Info pengguna dan tombol Keluar di bagian bawah.

### 6.7 Notifikasi (Alert)

- Muncul di atas konten, lebar penuh, padding 12px 16px, radius 6px, border 1px, teks 14px.
- Memakai warna status (latar tipis, teks berwarna, border sewarna yang lebih terang).
- Notifikasi berhasil hilang otomatis setelah 4 detik; error tetap tampil sampai ditutup.
- Pesan singkat dan spesifik, contoh: "Barang masuk berhasil disimpan. Stok Beras 5 kg kini 120."

### 6.8 Modal Konfirmasi

- Dipakai untuk aksi hapus dan konfirmasi opname.
- Lebar 440px, padding 24px, radius 8px, overlay `rgba(17,17,17,0.4)`.
- Judul 18px, isi 14px abu-abu, tombol di kanan bawah: Batal (sekunder) dan aksi (primer atau bahaya).

### 6.9 Filter dan Pencarian

- Baris filter di atas tabel: input pencarian (lebar 320px, ikon kaca pembesar di kiri), dropdown, dan filter tanggal sejajar dengan jarak 12px.
- Tombol "Terapkan" bertipe sekunder; tombol "Atur Ulang" bertipe teks.

## 7. Pola Halaman

### 7.1 Login

- Latar putih. Kartu form di tengah, lebar 400px, border 1px, padding 40px.
- Isi: nama aplikasi (judul 24px), teks pendukung satu baris, field email, field password, tombol primer lebar penuh.
- Tidak ada ilustrasi, tidak ada gambar latar.

### 7.2 Dashboard

1. Judul halaman "Dashboard" dan tanggal hari ini.
2. Empat kartu ringkasan sejajar: Total Barang, Total Pemasok, Masuk Hari Ini, Keluar Hari Ini.
3. Dua kolom di bawahnya: **Stok Menipis** (tabel 5 barang teratas dengan tautan "Lihat semua") dan **Transaksi Terakhir**.
4. Jarak antar bagian 32px.

### 7.3 Halaman Daftar (Barang, Pemasok, Transaksi)

Header halaman dengan tombol "Tambah ..." di kanan, baris filter, tabel, pagination.

### 7.4 Halaman Form (Tambah dan Ubah)

Judul halaman, tautan kembali di atas, kartu berisi form lebar maksimal 560px, tombol Simpan dan Batal di bawah.

### 7.5 Stok Opname

Tabel dengan kolom: Kode, Nama, Stok Sistem, Stok Fisik (input angka), Selisih (otomatis, merah jika negatif, kuning jika positif). Tombol "Konfirmasi Opname" di bawah, memunculkan modal konfirmasi.

### 7.6 Laporan

Filter tanggal atau bulan di atas, tombol "Cetak" dan "Ekspor" di kanan, tabel laporan dengan kolom Stok Awal, Masuk, Keluar, Stok Akhir. Baris total di bagian bawah dengan bobot 600. Stylesheet cetak: sembunyikan sidebar dan tombol, latar putih, font 12px.

## 8. Tulisan dan Bahasa Antarmuka

- Bahasa Indonesia baku yang ringkas dan ramah.
- Tombol memakai kata kerja: "Simpan", "Tambah Barang", "Hapus", bukan "OK" atau "Submit".
- Pesan error menjelaskan masalah dan solusi, contoh: "Jumlah melebihi stok tersedia (12). Kurangi jumlah barang keluar."
- Format tanggal: `24 Okt 2026`. Format angka: pemisah ribuan titik (`1.250`). Format mata uang: `Rp 25.000`.
- Hindari singkatan yang tidak umum.

## 9. Aksesibilitas

- Seluruh elemen interaktif dapat dijangkau dengan keyboard dan punya indikator fokus yang jelas.
- Setiap input punya `<label>` yang terhubung.
- Status tidak boleh hanya dibedakan lewat warna; selalu sertakan teks ("Menipis", "Habis").
- Ukuran area klik minimal 40x40px.
- Gambar atau ikon bermakna punya teks alternatif.

## 10. Implementasi CSS

Letakkan variabel berikut di `resources/css/app.css` dan pakai di seluruh komponen.

```css
:root {
  /* Warna */
  --color-bg: #FFFFFF;
  --color-text: #111111;
  --color-text-secondary: #6B7280;
  --color-primary: #2563EB;
  --color-primary-hover: #1D4ED8;
  --color-primary-soft: #EFF6FF;
  --color-border: #E5E7EB;
  --color-surface: #F9FAFB;

  --color-success: #15803D;
  --color-success-bg: #F0FDF4;
  --color-warning: #B45309;
  --color-warning-bg: #FFFBEB;
  --color-danger: #B91C1C;
  --color-danger-bg: #FEF2F2;

  /* Spasi */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 24px;
  --space-6: 32px;
  --space-7: 48px;
  --space-8: 64px;

  /* Bentuk */
  --radius-sm: 6px;
  --radius-md: 8px;
}

body {
  margin: 0;
  background: var(--color-bg);
  color: var(--color-text);
  font-family: 'Poppins', -apple-system, 'Segoe UI', Arial, sans-serif;
  font-size: 14px;
  line-height: 22px;
}

.btn {
  height: 40px;
  padding: 0 var(--space-4);
  border-radius: var(--radius-sm);
  font: 500 14px 'Poppins', sans-serif;
  cursor: pointer;
  transition: background-color 150ms ease, border-color 150ms ease;
}

.btn-primary {
  background: var(--color-primary);
  color: #fff;
  border: 0;
}
.btn-primary:hover { background: var(--color-primary-hover); }

.btn-secondary {
  background: var(--color-bg);
  color: var(--color-text);
  border: 1px solid var(--color-border);
}
.btn-secondary:hover { background: var(--color-surface); }

.input {
  width: 100%;
  height: 40px;
  padding: 0 var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  font: 400 14px 'Poppins', sans-serif;
  color: var(--color-text);
}
.input::placeholder { color: var(--color-text-secondary); }
.input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.card {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: var(--space-5);
}
```

## 11. Struktur Blade yang Disarankan

```
resources/views/
  layouts/
    app.blade.php          (sidebar + header + slot konten)
    guest.blade.php        (layout login)
  components/
    button.blade.php
    input.blade.php
    badge-status.blade.php
    alert.blade.php
    modal.blade.php
    table.blade.php
  dashboard.blade.php
  items/        (index, create, edit)
  suppliers/    (index, create, edit)
  stock-ins/    (index, create)
  stock-outs/   (index, create)
  stock-opnames/(index, create)
  reports/      (daily, monthly)
```

Gunakan Blade component agar tombol, input, dan badge tidak ditulis ulang di setiap halaman.

## 12. Daftar Periksa Sebelum Merge

- [ ] Latar putih, teks hitam, teks sekunder abu-abu
- [ ] Hanya satu warna aksen: `#2563EB`
- [ ] Font Poppins dimuat dan dipakai di seluruh elemen (termasuk input dan tombol)
- [ ] Tidak ada gradient, bayangan tebal, atau emoji
- [ ] Jarak mengikuti skala kelipatan 4px, kartu berjarak 24 sampai 32px
- [ ] Satu tombol primer per layar
- [ ] Status stok memakai teks dan warna
- [ ] Tabel angka rata kanan
- [ ] Form punya pesan error yang jelas
- [ ] Tampilan diperiksa pada lebar laptop dan tablet

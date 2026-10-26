# Contributing Guide

## Tujuan

Dokumen ini menjadi acuan kerja tim dalam mengembangkan InventarisKu secara konsisten, terstruktur, dan mudah direview. CONTRIBUTING menjelaskan **bagaimana tim membangunnya**.

## Dokumen Acuan

| Dokumen | Fungsi |
|---|---|
| `README.md` | Gambaran proyek, instalasi, konfigurasi, penggunaan |
| `docs/PRD.md` | Requirement, fitur, aturan bisnis, acceptance criteria |
| `docs/DESIGN.md` | Panduan UI/UX |
| `docs/ERD.mermaid` | Struktur dan relasi database |
| `CONTRIBUTING.md` | Workflow pengembangan tim |

## Prasyarat

- PHP 8.5.x atau versi yang kompatibel dengan `composer.json`
- Composer
- MySQL 8.0
- Git
- Node.js dan npm jika diperlukan untuk asset frontend

## Setup Development

```bash
git clone https://github.com/aditnurdiansyah/inventaris
cd inventaris
composer install
copy .env.example .env
php artisan key:generate
```

Atur database pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventaris
DB_USERNAME=root
DB_PASSWORD=
```

Kemudian:

```bash
php artisan migrate --seed
php artisan serve
```

Jangan commit `.env`, password, `APP_KEY`, API key, token, atau credential lainnya.

## Branch Strategy

Branch utama adalah `main`. Jangan mengembangkan fitur langsung pada `main`.

Gunakan format:

```text
feature/nama-fitur
fix/nama-masalah
refactor/nama-perubahan
docs/nama-dokumentasi
test/nama-pengujian
chore/nama-pekerjaan
```

Contoh:

```text
feature/stok-opname
feature/laporan-bulanan
fix/stock-out-validation
refactor/stock-service
docs/update-readme
```

### Alur kerja
 
```bash
# 1. Mulai dari main yang terbaru
git checkout main
git pull
 
# 2. Buat branch untuk satu fitur atau perbaikan
git checkout -b feature/barang-masuk
 
# 3. Kerjakan, lalu simpan perubahan dalam commit kecil
git add .
git commit -m "feat: validasi jumlah barang masuk"
 
# 4. Kirim branch ke GitHub
git push -u origin feature/barang-masuk
 
# 5. Buka Pull Request di GitHub (tombol "Compare & pull request")
```
 
Setelah PR di-merge: hapus branch, lalu `git checkout main && git pull` sebelum memulai pekerjaan berikutnya.
 
**Sebelum `git add .`, selalu jalankan `git status`** dan pastikan tidak ada `.env`, folder `vendor`, atau file pribadi ikut tercatat.

Intinya sebelum mulai:

```bash
git checkout main
git pull origin main
git checkout -b feature/nama-fitur
```

Satu branch sebaiknya fokus pada satu pekerjaan.

## Commit Convention

Format:

```text
type: deskripsi singkat
```

| Type | Penggunaan |
|---|---|
| `feat` | Fitur baru |
| `fix` | Perbaikan bug |
| `refactor` | Perubahan struktur kode |
| `docs` | Dokumentasi |
| `test` | Pengujian |
| `style` | Formatting/style |
| `chore` | Maintenance/configuration |

Contoh:

```text
feat: add monthly inventory report
fix: prevent stock out above available stock
refactor: move stock calculation to StockService
docs: update installation guide
test: add stock service validation tests
```

Hindari commit seperti `update`, `fix`, `coba`, `final`, atau `perbaikan`.

## Aturan Pengembangan Laravel

### Controller

Controller menangani request dan response. Hindari business logic kompleks yang seharusnya berada di service.

```text
Controller
    ↓
Service
    ↓
Model / Database
```

### StockService

Perubahan stok harus menggunakan `StockService` agar business logic tetap terpusat, terutama untuk barang masuk, barang keluar, stok opname, dan penyesuaian stok.

### Database Transaction

Operasi yang mengubah beberapa data terkait harus mempertimbangkan transaction:

```php
DB::transaction(function () {
    // perubahan data
});
```

Operasi stok yang berpotensi mengalami concurrent update harus menggunakan mekanisme locking yang sesuai.

### Validation

Input wajib divalidasi sebelum diproses:

```php
$request->validate([
    'quantity' => ['required', 'integer', 'min:1'],
]);
```

## Database dan Migration

Perubahan struktur database harus dibuat melalui migration:

```bash
php artisan make:migration add_column_to_items_table
php artisan migrate
```

Aturan:
- jangan mengandalkan perubahan manual pada database lokal;
- hindari mengubah migration lama yang sudah digunakan bersama tim;
- gunakan migration baru untuk perubahan berikutnya;
- pertimbangkan data existing ketika menambah field wajib;
- update `docs/ERD.mermaid` jika struktur database berubah.

## Seeder

Data demo/development dapat disediakan melalui seeder:

```bash
php artisan db:seed
php artisan migrate --seed
```

Gunakan data dummy. Jangan memasukkan password akun nyata, API key, token, atau credential layanan eksternal.

## UI/UX

Perubahan UI harus mengikuti `docs/DESIGN.md`.

Prinsip:
- sederhana;
- mudah dipahami;
- konsisten;
- responsif;
- hierarchy informasi jelas;
- tidak menggunakan gradient jika tidak ditentukan desain.

Warna utama:

```text
Primary   : #2563EB
Secondary : #0F172A
Neutral   : #64748B
```

Gunakan typography yang konsisten dengan desain proyek, seperti Poppins atau Montserrat.

## Testing

Sebelum Pull Request, lakukan pengujian yang relevan:

```bash
php artisan route:list
php artisan migrate:status
php artisan test
```

Jika perubahan frontend membutuhkan build:

```bash
npm run build
```

Untuk fitur inventory, minimal periksa tambah/edit/hapus data, validation, kondisi data kosong, perubahan stok, laporan, hak akses Admin/Staff, dan tampilan pada ukuran layar yang relevan.

## Pull Request

Setiap perubahan yang masuk ke `main` sebaiknya melalui Pull Request.

Contoh judul:

```text
feat: add monthly inventory report
fix: prevent invalid stock out
docs: improve project setup documentation
```

PR minimal menjelaskan:
1. apa yang diubah;
2. alasan perubahan;
3. cara testing;
4. perubahan database;
5. perubahan UI;
6. hal yang perlu diperhatikan reviewer.

Contoh:

```markdown
## Summary
- Menambahkan laporan stok bulanan.
- Menambahkan filter bulan.
- Menambahkan total barang masuk dan keluar.

## Testing
- [x] Manual testing
- [x] php artisan test

## Database
- Tidak ada perubahan database.

## UI
- Menambahkan halaman laporan bulanan.
```

## Code Review

Reviewer memeriksa kesesuaian dengan PRD, business logic, validation, authorization, keamanan, query database, potensi error, konsistensi Laravel, UI/UX, dampak terhadap fitur lain, migration, dan testing.

Review bukan hanya memastikan aplikasi berjalan, tetapi juga memastikan perubahan aman dan maintainable.

## Merge

Sebelum merge:
- Pull Request sudah dibuat;
- testing selesai;
- tidak ada unresolved conflict;
- reviewer menyetujui;
- migration diperiksa jika ada;
- dokumentasi diperbarui jika diperlukan.

Setelah merge, branch fitur dapat dihapus jika sudah tidak diperlukan.

## Menangani Conflict

```bash
git checkout main
git pull origin main
git checkout feature/nama-fitur
git merge main
```

Setelah conflict:
1. buka file yang conflict;
2. pahami kedua perubahan;
3. gabungkan perubahan yang benar;
4. hapus conflict marker;
5. jalankan testing;
6. commit penyelesaian conflict.

Jangan memilih seluruh perubahan dari salah satu branch tanpa memahami dampaknya.

## Keamanan

`.env` tidak boleh di-commit. Yang boleh di-commit adalah `.env.example`.

Jangan commit:
- password database;
- `APP_KEY` asli;
- API key;
- access token;
- secret key;
- credential akun.

Jika secret sudah terlanjur masuk repository, anggap secret tersebut terekspos dan lakukan rotasi/revoke.

## Dokumentasi

Perbarui dokumentasi jika perubahan kode memengaruhinya:

| Perubahan | Dokumen |
|---|---|
| Fitur/requirement | `docs/PRD.md` |
| Setup/penggunaan | `README.md` |
| UI | `docs/DESIGN.md` |
| Database | `docs/ERD.mermaid` |
| Workflow tim | `CONTRIBUTING.md` |

## Definition of Done

- [ ] Fitur sesuai requirement.
- [ ] Business rule diterapkan.
- [ ] Validation diterapkan.
- [ ] Authorization diperiksa.
- [ ] Migration dibuat jika diperlukan.
- [ ] Seeder diperbarui jika diperlukan.
- [ ] Testing selesai.
- [ ] Tidak merusak fitur terkait.
- [ ] UI mengikuti `DESIGN.md`.
- [ ] Dokumentasi diperbarui jika diperlukan.
- [ ] Pull Request dibuat.
- [ ] Code review selesai.
- [ ] Tidak ada unresolved conflict.
- [ ] Siap di-merge.

## Alur Kerja Tim

```text
Baca PRD / Issue
      ↓
Buat branch
      ↓
Implementasi
      ↓
Testing
      ↓
Commit
      ↓
Push branch
      ↓
Pull Request
      ↓
Code Review
      ↓
Perbaikan jika diperlukan
      ↓
Approval
      ↓
Merge ke main
```

Contoh:

```bash
git checkout main
git pull origin main
git checkout -b feature/stok-opname

# implementasi

php artisan test

git add .
git commit -m "feat: add stock opname feature"
git push -u origin feature/stok-opname
```

## Checklist Kontributor

```text
[ ] Branch sudah sesuai
[ ] Commit sudah jelas
[ ] Tidak ada .env/secrets yang ikut ter-commit
[ ] Migration sudah diperiksa
[ ] Validation sudah diperiksa
[ ] Authorization sudah diperiksa
[ ] Logic stok sudah aman
[ ] Testing sudah dilakukan
[ ] UI sudah diperiksa
[ ] Dokumentasi diperbarui jika diperlukan
[ ] Pull Request memiliki deskripsi yang jelas
```

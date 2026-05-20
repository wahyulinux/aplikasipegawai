# Sistem Penggajian Pegawai (Payroll App)

Aplikasi manajemen penggajian pegawai sederhana yang dibangun dengan Laravel 11 dan FrankenPHP. Sistem ini mencakup pengelolaan data pegawai, input komponen gaji yang mendetail, ekspor laporan, serta alur persetujuan HRD dengan tanda tangan digital (QR Code).

## 🚀 Stack Teknologi

- **Backend:** [Laravel 11](https://laravel.com/) (PHP 8.3)
- **Application Server:** [FrankenPHP](https://frankenphp.dev/) (Modern PHP server berbasis Caddy)
- **Database:** PostgreSQL 15 (diubah dari MySQL)
- **Session & Cache:** Redis
- **Frontend:** Blade Templates + [Tailwind CSS](https://tailwindcss.com/)
- **Infrastructure:** Docker Compose
- **Excel Handling:** PhpSpreadsheet

## 🛠 Fitur Utama

- **Manajemen Pegawai:** CRUD data identitas pegawai (NIP, Nama, Email, Jabatan, Nomor Rekening).
- **Komponen Gaji Lengkap:** Mendukung Gaji Pokok, Tunjangan, Uang Makan, Kinerja, PSB, Kerajinan, Extra Fooding, Insentif Jalur, Lembur, dan Piket.
- **Komponen Potongan:** Mendukung Potongan Kinerja, BPJS Ketenagakerjaan, BPJS Kesehatan, dan Potongan Pinjaman.
- **Kalkulasi Otomatis:** Sistem otomatis menghitung Total Penghasilan, Total Potongan, dan Gaji Bersih (Take Home Pay).
- **Approval HRD:** Data gaji terkunci setelah disetujui untuk menjaga integritas data.
- **Digital Signature:** Slip gaji dilengkapi dengan QR Code unik sebagai verifikasi digital setelah di-approve oleh HRD.
- **Cetak Slip Gaji:** Fitur cetak slip gaji satuan maupun cetak masal dengan penyesuaian tata letak otomatis. Mendukung pencetakan slip berstatus *Draft* (belum disetujui).
- **Export Excel:** Laporan data gaji dapat diunduh dalam format `.xlsx` menggunakan template standar (`Payroll.xlsx`) yang secara otomatis diisi oleh sistem.

## 📦 Prasyarat

- Docker & Docker Compose
- Git

## ⚙️ Cara Instalasi

### 1. Persiapan Awal
```bash
git clone <repository-url>
cd aplikasipegawai
cp .env.example .env
```

### 2. Jalankan Container
Aplikasi ini sudah dipaketkan dalam container, termasuk database PostgreSQL.
```bash
docker-compose up -d --build
```

### 3. Install Dependensi & Migrasi Database
Masuk ke dalam container aplikasi untuk menginstall paket composer dan menjalankan migrasi database:
```bash
docker exec -it aplikasipegawai-app composer install
docker exec -it aplikasipegawai-app php artisan migrate:fresh
```

---

## 🛠 Menjalankan Aplikasi

### A. Mode Development (Standar)
Cocok untuk pengembangan.
```bash
docker-compose up -d
```
Akses: `http://localhost:8000`

### B. Mode Production (Worker Mode)
Menggunakan **FrankenPHP Worker Mode** via Laravel Octane (Jika dikonfigurasi).
```bash
docker-compose -f docker-compose.prod.yml up -d --build
```

---

## 📝 Catatan Penggunaan

- **Template Export Excel:** Jika ingin merubah desain file hasil export, silakan edit file `Payroll.xlsx` di root folder aplikasi. Pastikan baris pertama tetap sebagai header, karena sistem mulai mengisi data dari baris ke-2.
- **Approval:** Data payroll yang sudah berstatus **Approved** tidak dapat diedit atau dihapus untuk alasan keamanan.
- **Digital Signature:** Barcode/QR Code pada slip gaji hanya akan muncul pada data yang sudah mendapatkan approval dari HRD. Data yang belum di-approve akan dilabeli sebagai **Belum Disetujui / DRAFT**.

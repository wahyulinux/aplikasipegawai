# Sistem Penggajian Pegawai (Payroll App)

Aplikasi manajemen penggajian pegawai sederhana yang dibangun dengan Laravel 11 dan FrankenPHP. Sistem ini mencakup pengelolaan data pegawai, input komponen gaji yang mendetail, serta alur persetujuan HRD dengan tanda tangan digital (QR Code).

## 🚀 Stack Teknologi

- **Backend:** [Laravel 11](https://laravel.com/) (PHP 8.3)
- **Application Server:** [FrankenPHP](https://frankenphp.dev/) (Modern PHP server berbasis Caddy)
- **Database:** MySQL 8.0
- **Session & Cache:** Redis
- **Frontend:** Blade Templates + [Tailwind CSS](https://tailwindcss.com/)
- **Infrastructure:** Docker Compose

## 🛠 Fitur Utama

- **Manajemen Pegawai:** CRUD data identitas pegawai (NIP, Nama, Jabatan).
- **Komponen Gaji Lengkap:** Mendukung Gaji Pokok, Tunjangan, Uang Makan, Kinerja, PSB, Kerajinan, Extra Fooding, Insentif Jalur, Lembur, dan Piket.
- **Kalkulasi Otomatis:** Sistem otomatis menghitung Total Penghasilan, Total Potongan, dan Gaji Bersih (Take Home Pay).
- **Approval HRD:** Data gaji terkunci setelah disetujui untuk menjaga integritas data.
- **Digital Signature:** Slip gaji dilengkapi dengan QR Code unik sebagai verifikasi digital setelah di-approve oleh HRD.
- **Ready to Print:** Desain slip gaji yang bersih dan siap cetak.

## 📦 Prasyarat

- Docker & Docker Desktop
- Git

## ⚙️ Cara Instalasi

### 1. Persiapan Awal
```bash
git clone <repository-url>
cd aplikasipegawai
cp .env.example .env
docker compose up -d --build
```

### 2. Setup Laravel Octane (Untuk Worker Mode)
Aplikasi ini mendukung **FrankenPHP Worker Mode** melalui Laravel Octane untuk performa maksimal.
```bash
docker compose exec app composer require laravel/octane
docker compose exec app php artisan octane:install --server=frankenphp
```

---

## 🛠 Menjalankan Aplikasi

### A. Mode Development (Standar)
Cocok untuk pengembangan. Perubahan kode langsung terlihat tanpa build ulang.
```bash
docker compose up -d
```
Akses: `http://localhost:8000`

### B. Mode Production (Worker Mode)
Menggunakan **FrankenPHP Worker Mode** via Laravel Octane. Performa sangat tinggi dan efisien.
```bash
docker compose -f docker-compose.prod.yml up -d --build
```
Akses: `http://localhost` (Port 80)

---

## 📦 Perintah Penting Lainnya
- **Migrasi Database:** `docker compose exec app php artisan migrate`
- **Tinker (CLI):** `docker compose exec app php artisan tinker`
- **Reset Database:** `docker compose exec app php artisan migrate:fresh`

## 🐳 Struktur Docker

- **App Service:** Menjalankan Laravel menggunakan FrankenPHP di port 8000.
- **DB Service:** Menjalankan MySQL 8.0 (terisolasi dalam network internal).
- **Redis Service:** Digunakan untuk session management dan caching.

## 📝 Catatan Penggunaan

- Data payroll yang sudah berstatus **Approved** tidak dapat diedit atau dihapus untuk alasan audit.
- Barcode/QR Code pada slip gaji hanya akan muncul pada data yang sudah mendapatkan approval dari HRD.

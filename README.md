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

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd aplikasipegawai
   ```

2. **Siapkan Environment**
   Salin file `.env.example` atau buat file `.env` baru:
   ```bash
   cp .env.example .env
   ```
   *Pastikan konfigurasi database di `.env` sesuai dengan `docker-compose.yml`.*

3. **Build dan Jalankan Docker**
   ```bash
   docker compose up -d --build
   ```

4. **Instal Dependencies Laravel**
   ```bash
   docker compose exec app composer install
   ```

5. **Generate Application Key**
   ```bash
   docker compose exec app php artisan key:generate
   ```

6. **Jalankan Migrasi Database**
   ```bash
   docker compose exec app php artisan migrate
   ```

7. **Akses Aplikasi**
   Buka browser dan akses: `http://localhost:8000`

## 🐳 Struktur Docker

- **App Service:** Menjalankan Laravel menggunakan FrankenPHP di port 8000.
- **DB Service:** Menjalankan MySQL 8.0 (terisolasi dalam network internal).
- **Redis Service:** Digunakan untuk session management dan caching.

## 📝 Catatan Penggunaan

- Data payroll yang sudah berstatus **Approved** tidak dapat diedit atau dihapus untuk alasan audit.
- Barcode/QR Code pada slip gaji hanya akan muncul pada data yang sudah mendapatkan approval dari HRD.

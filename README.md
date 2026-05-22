# Sistem Penggajian Pegawai (Payroll App)

Aplikasi manajemen penggajian pegawai berskala menengah yang dibangun dengan Laravel 11 dan FrankenPHP. Sistem ini dirancang dengan prinsip *Maker-Checker* dan pemisahan tugas (Segregation of Duties) yang ketat melalui sistem multi-role, meliputi pengelolaan gaji, manajemen pinjaman karyawan (kasbon), hingga tanda tangan digital.

## 🚀 Stack Teknologi

- **Backend:** [Laravel 11](https://laravel.com/) (PHP 8.3)
- **Application Server:** [FrankenPHP](https://frankenphp.dev/) (Modern PHP server berbasis Caddy)
- **Database:** PostgreSQL 15
- **Session & Cache:** Redis
- **Frontend:** Blade Templates + [Tailwind CSS](https://tailwindcss.com/) + Alpine.js
- **Infrastructure:** Docker & Docker Compose
- **Excel Handling:** PhpSpreadsheet

## 👥 Sistem Hak Akses (Multi-Role)

Aplikasi ini memiliki 5 tingkat hak akses yang saling terisolasi:

1. **Superadmin:** Akses khusus IT. Hanya bisa mengelola akun operasional (Tambah/Edit/Nonaktifkan Staff, HRD, Finance). Tidak bisa melihat data keuangan.
2. **Staff (Maker):** Mengelola data master pegawai, menginput pengajuan gaji, dan melakukan perhitungan.
3. **Finance (Approver Pinjaman):** Menyetujui atau menolak pengajuan pinjaman uang dari pegawai.
4. **HRD (Approver Gaji):** Melakukan validasi akhir dan menyetujui (Approve) draf gaji yang dibuat oleh Staff.
5. **Pegawai:** Hanya bisa login untuk mengajukan pinjaman dan melakukan Konfirmasi Penerimaan Gaji pada slip pribadinya.

## 🛠 Fitur Utama

- **Sistem Pinjaman Terintegrasi:** Pegawai dapat memiliki maksimal 3 pinjaman aktif. Sistem akan **otomatis memotong gaji** setiap bulan hingga sisa hutang lunas.
- **Validasi QR Publik:** Slip gaji dilengkapi QR Code. Pihak eksternal (seperti Bank) dapat melakukan *scan* untuk memvalidasi keaslian slip gaji tanpa perlu login.
- **Digital Acknowledgment:** Fitur *"Konfirmasi Terima Gaji"* oleh pegawai yang tercatat hingga hitungan detik (Timestamp) sebagai pengganti tanda tangan basah.
- **Export Excel Pro:** Export rekap gaji dalam format `.xlsx` menggunakan file `Payroll.xlsx` asli sebagai template, sehingga format desain perusahaan tetap terjaga.
- **Mobile Responsive:** Antarmuka aplikasi otomatis menyesuaikan dengan layar HP/Tablet (Menu Hamburger & Tabel responsif).

## 📦 Prasyarat Instalasi

- Docker & Docker Compose
- Git

## ⚙️ Cara Instalasi (Development)

1. **Persiapan Awal**
```bash
git clone <repository-url>
cd aplikasipegawai
cp .env.example .env
```

2. **Jalankan Container & Build Image**
```bash
docker-compose up -d --build
```

3. **Install Dependensi & Migrasi Database**
```bash
docker exec -it aplikasipegawai-app composer install
docker exec -it aplikasipegawai-app php artisan migrate:fresh --seed
```

Akses aplikasi di: `http://localhost:8000`

## 🔑 Akun Login Default (Seeder)

Setelah menjalankan perintah seeder di atas, Anda dapat login menggunakan akun berikut:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Superadmin** | `admin@payroll.com` | `admin123` |
| **Staff** | `staff@payroll.com` | `password` |
| **HRD** | `hrd@payroll.com` | `password` |
| **Finance**| `finance@payroll.com` | `password` |

*(Catatan: Akun login pegawai dibuat secara otomatis oleh sistem saat Staff mendaftarkan pegawai baru. Password default adalah NIP pegawai).*

## 🚀 Cara Instalasi (Production / Worker Mode)

Untuk *deployment* ke server *production*, gunakan konfigurasi Octane + FrankenPHP untuk performa maksimal:

```bash
docker-compose -f docker-compose.prod.yml down -v
docker-compose -f docker-compose.prod.yml up -d --build
docker exec -it aplikasipegawai-prod-app php artisan migrate --force
```

## 📝 Catatan Integritas Data
- Jika slip gaji dihapus oleh Staff, sisa hutang pinjaman **tidak akan** bertambah kembali untuk menjaga integritas data akuntansi satu arah.
- Akun user tidak dapat dihapus, melainkan hanya di-Nonaktifkan (*Deactivate*) oleh Superadmin untuk menjaga histori data *"Approved By"*.

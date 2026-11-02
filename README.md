# Jangkau Samawa

> **Sistem Informasi Pengaduan Kesejahteraan Sosial**  
> Dinas Sosial Kabupaten Sumbawa

Aplikasi web terpadu untuk penerimaan, verifikasi, disposisi, tindak lanjut, dan pelacakan pengaduan masalah kesejahteraan sosial masyarakat di Kabupaten Sumbawa.

---

## 🛠️ Tech Stack

- **Backend:** [PHP 8.4+](https://www.php.net/) & [Laravel 13.x](https://laravel.com/)
- **Frontend & Reaktivitas:** [Livewire 4.x](https://livewire.laravel.com/), [Livewire Volt](https://livewire.laravel.com/docs/volt), [Flux UI](https://fluxui.dev/), [Tailwind CSS v4](https://tailwindcss.com/)
- **Bundler:** [Vite](https://vitejs.dev/)
- **Database:** [MySQL](https://www.mysql.com/) / MariaDB
- **Pengujian:** [Pest PHP](https://pestphp.com/)
- **Notifikasi & OTP:** WhatsApp Gateway via [Fonnte API](https://fonnte.com/)

---

## 📋 Prasyarat Sistem

Pastikan sistem lokal Anda telah terpasang:
- PHP >= 8.4 dengan ekstensi wajib: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `json`, `bcmath`, `xml`, `ctype`, serta `pdo_sqlite` & `sqlite3` (untuk runner pengujian memori).
- [Composer](https://getcomposer.org/) >= 2.x
- [Node.js](https://nodejs.org/) >= 20.x & npm
- Server database MySQL/MariaDB

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah berikut untuk menjalankan proyek di lingkungan pengembangan lokal:

### 1. Kloning Repositori
```bash
git clone <repository-url>
cd sip-dinsos-web-v1
```

### 2. Pasang Dependensi PHP (Composer)
```bash
composer install
```

### 3. Pasang Dependensi Node.js (npm)
```bash
npm install
```

### 4. Konfigurasi Environment File
Salin file konfigurasi contoh `.env.example` menjadi `.env`:
- **Windows (PowerShell / CMD):**
  ```powershell
  copy .env.example .env
  ```
- **Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

Buka file `.env` dan sesuaikan koneksi database serta token Fonnte:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sip_dinsos
DB_USERNAME=root
DB_PASSWORD=

# Token Fonnte untuk notifikasi dan OTP WhatsApp
FONNTE_TOKEN=masukkan_token_fonnte_anda
FONNTE_ENABLED=true
```

### 5. Buat Application Key
```bash
php artisan key:generate
```

### 6. Jalankan Migrasi dan Database Seeder
Pastikan database MySQL (misal: `db_sip_dinsos`) sudah dibuat, lalu jalankan:
```bash
php artisan migrate --seed
```

### 7. Build Aset Frontend
Jalankan kompilasi aset frontend:
```bash
# Untuk produksi / build statis:
npm run build

# Atau untuk live hot-reload saat development:
npm run dev
```

### 8. Jalankan Server Pengembangan
```bash
php artisan serve
```
Aplikasi dapat diakses melalui browser di: `http://localhost:8000`

---

## 👥 Akun Pengguna Bawaan (Hasil Seeder)

Untuk keperluan pengujian lokal, seeder telah menyediakan 4 role pengguna:

| Role | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin** | `ikbalmindarsah22@gmail.com` | `password` | Pengelolaan pengguna, kategori, & monitoring penuh |
| **Petugas Penerima** | `penerima@jangkausamawa.id` | `password` | Verifikasi awal, validasi status, & penerusan aduan |
| **Kepala Dinas** | `kadissosial@gmail.com` | `password` | Review aduan masuk & pembuatan lembar disposisi |
| **Petugas Pelaksana** | `pelaksana@jangkausamawa.id` | `password` | Eksekusi tindak lanjut disposisi & penyelesaian aduan |

---

## 🧪 Pengujian Otomatis (Tests)

Proyek ini menggunakan **Pest PHP** untuk suite pengujian fungsional dan unit:

```bash
# Menjalankan seluruh test suite dengan driver SQLite in-memory:
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/pestphp/pest/bin/pest

# Atau menggunakan artisan test runner:
php -d extension=pdo_sqlite -d extension=sqlite3 artisan test --compact
```

---

## 🎨 Standar Format Kode

Format kode PHP mengikuti standar Laravel Pint:
```bash
vendor/bin/pint --format agent
```

---

## 🛡️ Keamanan & Git Safety

- File kredensial (`.env`, `.env.*`, `auth.json`, `*.key`) diabaikan oleh Git melalui `.gitignore`.
- Selalu perbarui `.env.example` jika menambahkan variabel lingkungan baru tanpa mengisi nilai rahasia/kredensial asli.


# Muslim Clock Web

Aplikasi web jam sholat masjid berbasis **PHP + MySQL** yang dirancang untuk dijalankan di **XAMPP (Windows)** dan ditampilkan layar penuh (TV/monitor) di area masjid.

![preview](assets/img/default-bg.svg)

## Fitur

- **3 Layout pilihan** lewat admin: Cinema (slideshow + sidebar), Minimal (jam digital raksasa), Mosque (tema masjid dengan ornamen)
- **Picker font** di admin: 9 font tampilan (Inter, Poppins, Plus Jakarta, Manrope, Outfit, Sora, Lexend, Montserrat, Rubik) + 9 font digital (Orbitron, JetBrains Mono, Space Mono, Major Mono, Share Tech, IBM Plex Mono, Anton, Bebas Neue, dll.)
- Tampilan layar penuh: jam analog + digital, header masjid, jadwal sholat 6 waktu, tanggal Masehi & Hijriyah (format "1 Ramadhan 1447 H").
- **Slideshow** foto/video latar (multi-file upload, urutan, aktif/nonaktif).
- **Running text** lower-third — banyak baris, kecepatan animasi diatur.
- **Cuplikan Al-Qur'an sebagai marquee** — ayat panjang tidak akan terpotong.
- **Lokasi presisi** lewat pencarian kota (OpenStreetMap), jadwal dihitung pakai metode **Kemenag (Aladhan method = 20)**.
- **Jadwal imam** Senin–Minggu untuk Subuh–Isya, plus Sholat Jum'at (imam, khatib, bilal). Nama imam tampil otomatis pada kartu waktu sholat.
- **Overlay adzan**: ketika masuk waktu sholat, layar berubah menampilkan pesan kustom + countdown adzan, lalu otomatis lanjut countdown **iqomah**.
- **Preset tema warna** (9 preset) + warna kustom (primary & accent).
- **Installer wizard** ala WordPress — 3 langkah: persyaratan, database, akun admin & masjid.
- Login admin terlindungi (CSRF, password hashing).
- Tombol **Test Adzan** di pojok kiri-bawah untuk simulasi cepat.

## Persyaratan

- XAMPP (PHP ≥ 7.4 dengan ekstensi `pdo_mysql`, `curl`, `mbstring`, `json`)
- MariaDB / MySQL (sudah termasuk di XAMPP)
- Browser modern (Chrome/Edge/Firefox) untuk tampilan layar
- Koneksi internet ketika pertama kali memuat jadwal & ayat (akan di-cache)

## Instalasi (XAMPP Windows)

### 1. Salin folder proyek

Salin folder `muslimclockweb/` ke `C:\xampp\htdocs\` sehingga path-nya menjadi:

```
C:\xampp\htdocs\muslimclockweb
```

### 2. (Opsional tapi direkomendasikan) Setup Virtual Host

Edit file Apache: `C:\xampp\apache\conf\extra\httpd-vhosts.conf` dan tambahkan:

```apache
<VirtualHost *:80>
    ServerName masjid.test
    DocumentRoot "C:/xampp/htdocs/muslimclockweb"
    <Directory "C:/xampp/htdocs/muslimclockweb">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Lalu edit file `C:\Windows\System32\drivers\etc\hosts` (Run as Administrator), tambahkan baris:

```
127.0.0.1   masjid.test
```

Restart Apache dari XAMPP Control Panel.

### 3. Jalankan Installer

Buka di browser:

- Tanpa vhost: `http://localhost/muslimclockweb/install.php`
- Dengan vhost: `http://masjid.test/install.php`

Ikuti **3 langkah wizard**:

1. **Persyaratan** — pastikan semua hijau.
2. **Database** — isi host (`localhost`), user (`root`), password (kosong di XAMPP default), nama database (mis. `muslimclock`). Database akan dibuat otomatis bila belum ada.
3. **Admin & Masjid** — isi nama masjid, alamat, dan akun admin (username + password).

Setelah selesai, **hapus file `install.php`** demi keamanan (atau buka kembali dengan `install.php?force=1` jika ingin install ulang).

### 4. Akses Aplikasi

- Tampilan layar (untuk TV / monitor): `http://masjid.test/` atau `http://localhost/muslimclockweb/`
  Tekan **F11** atau klik dua kali untuk masuk **fullscreen**.
- Admin panel: `http://masjid.test/admin/login.php`

## Pengaturan Awal yang Disarankan

Setelah login admin, atur secara berurutan:

1. **Lokasi & Jadwal** — cari kota Anda (mis. "Depok"), pilih hasil → koordinat akan terisi otomatis. Pilih metode **Kemenag Indonesia (20)**.
2. **Slideshow** — unggah 3–5 foto masjid Anda.
3. **Running Text** — tambahkan info kegiatan masjid.
4. **Jadwal Imam** — isi nama imam untuk tiap hari & sholat.
5. **Tema Warna** — pilih preset yang sesuai.
6. **Adzan & Iqomah** — atur pesan dan durasi countdown.
7. **Cuplikan Al-Qur'an** — biarkan mode `auto`, atau tambahkan koleksi manual.

## Struktur Folder

```
muslimclockweb/
├── admin/             # Admin panel (login, settings, slides, dll.)
├── api/               # Endpoint JSON: prayer, quran, locations
├── assets/
│   ├── css/           # screen.css, admin.css
│   ├── js/            # clock.js
│   ├── img/           # default background
│   └── uploads/       # slideshow, logo, cache
├── config/
│   ├── config.sample.php
│   └── config.php     # dibuat otomatis oleh installer
├── includes/          # db.php, auth.php, functions.php, schema.sql
├── index.php          # tampilan layar (display)
├── install.php        # installer wizard
└── README.md
```

## Catatan Teknis

- **Jadwal sholat** dihitung lewat [Aladhan API](https://aladhan.com/) menggunakan koordinat & metode dari setting; respons di-cache 6 jam per tanggal.
- **Hijriyah** menggunakan `Intl.DateTimeFormat('id-u-ca-islamic-umalqura', ...)` di sisi browser (tidak butuh API).
- **Cuplikan Quran** mode auto memilih ayat dari daftar terkurasi dan diambil dari `alquran.cloud` (Arab + terjemahan Indonesia), lalu di-cache permanen per ayat.
- **Adzan trigger** dijalankan di sisi browser tepat ketika jam lokal cocok dengan waktu sholat hari ini (mengabaikan Syuruq).
- **Keamanan**: CSRF token untuk semua form admin, password disimpan dengan `password_hash()`. File `config.php` diblokir oleh `.htaccess`.

## Troubleshooting

- **"DB connection failed"** — pastikan MySQL service di XAMPP sudah jalan, dan kredensial benar (root / kosong).
- **Tampilan tetap pakai jadwal lama** — buka admin → Lokasi & Jadwal → simpan ulang (cache akan dibersihkan).
- **Slideshow blank** — pastikan folder `assets/uploads/slideshow` writable.
- **Hijri kosong** — gunakan browser modern (Chrome/Edge ≥ 95).

## Lisensi

Proyek ini ditulis untuk keperluan dakwah dan operasional masjid. Bebas digunakan & dimodifikasi.

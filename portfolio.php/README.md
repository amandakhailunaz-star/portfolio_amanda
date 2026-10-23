# Portofolio Amanda Nazwa (versi PHP)

Website portofolio dengan form kontak yang menyimpan pesan ke MySQL (Aiven) dan dashboard admin.

## Teknologi
- PHP 8+ (ekstensi PDO MySQL), tanpa framework
- HTML5, CSS, Bootstrap 5.3.3 (CDN)

## Menjalankan lokal
1. Salin `.env.example` menjadi `.env` lalu isi nilainya.
2. Jalankan: `php -S localhost:8000`
3. Buka http://localhost:8000 — admin di http://localhost:8000/admin/

## Tabel database
Tabel `pesan` dengan kolom: `id`, `nama`, `gmail`, `isi`, `tanggal_pengiriman_pesan`.

## Hosting
Gunakan hosting PHP (cPanel/Apache, dll). Vercel tidak mendukung PHP secara bawaan.

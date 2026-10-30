<?php
declare(strict_types=1);

// Koneksi ke MySQL lokal XAMPP
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'portfolio_amanda';

try {
    $db = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Koneksi database gagal. Pastikan MySQL XAMPP menyala.');
}

// Pastikan form dikirim menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// Ambil data dari form
$nama  = trim((string) ($_POST['nama'] ?? ''));
$email = trim((string) ($_POST['gmail'] ?? ''));
$isi   = trim((string) ($_POST['isi'] ?? ''));

// Validasi input
if ($nama === '' || $email === '' || $isi === '') {
    echo '<script>
        alert("Semua kolom wajib diisi!");
        window.location.href = "contact.php";
    </script>';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '<script>
        alert("Format email tidak valid!");
        window.location.href = "contact.php";
    </script>';
    exit;
}

try {
    // Simpan pesan ke tabel tbl_kontak
    $stmt = $db->prepare(
        'INSERT INTO tbl_kontak (nama, email, isi)
         VALUES (?, ?, ?)'
    );

    $stmt->execute([$nama, $email, $isi]);

    echo '<script>
        alert("Pesan berhasil dikirim!");
        window.location.href = "contact.php";
    </script>';
    exit;

} catch (PDOException $e) {
    error_log('Gagal menyimpan pesan: ' . $e->getMessage());

    echo '<script>
        alert("Pesan gagal disimpan. Periksa struktur tabel database.");
        window.location.href = "contact.php";
    </script>';
    exit;
}

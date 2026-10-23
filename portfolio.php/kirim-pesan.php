<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

function alert_dan_kembali(string $pesan, int $kode): void
{
    http_response_code($kode);
    echo '<script>alert(' . json_encode($pesan, JSON_HEX_TAG | JSON_HEX_AMP) . ');'
       . 'window.location.href = "contact.php";</script>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

$nama  = trim((string) ($_POST['nama'] ?? ''));
$gmail = trim((string) ($_POST['gmail'] ?? ''));
$isi   = trim((string) ($_POST['isi'] ?? ''));

if ($nama === '' || $gmail === '' || $isi === '') {
    alert_dan_kembali('Semua kolom wajib diisi!', 400);
}

try {
    $db   = koneksi_database();
    $stmt = $db->prepare('INSERT INTO pesan (nama, gmail, isi) VALUES (?, ?, ?)');
    $stmt->execute([$nama, $gmail, $isi]);
} catch (Throwable $ex) {
    error_log('Gagal menyimpan pesan: ' . $ex->getMessage());
    alert_dan_kembali('Pesan gagal dikirim. Silakan coba lagi.', 500);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesan Berhasil</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; justify-content: center; align-items: center;
               background: #f9f3ef; font-family: Arial, sans-serif; color: #5b4038; }
        .card { background: white; padding: 40px 30px; border-radius: 20px; text-align: center;
                max-width: 400px; margin: 20px; box-shadow: 0 8px 25px #704b3b18; }
        .icon { font-size: 60px; margin-bottom: 15px; }
        h1 { color: #b66f7f; font-size: 25px; }
        p { line-height: 1.7; }
        a { display: inline-block; margin-top: 15px; padding: 12px 24px; background: #b66f7f;
            color: white; text-decoration: none; border-radius: 10px; }
        a:hover { background: #965467; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">&#10004;</div>
        <h1>Pesan Berhasil Dikirim!</h1>
        <p>Terima kasih sudah menghubungi saya. Pesan kamu berhasil disimpan.</p>
        <a href="contact.php">Kembali ke Portfolio</a>
    </div>
</body>
</html>

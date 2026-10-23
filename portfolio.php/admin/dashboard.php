<?php
declare(strict_types=1);
require __DIR__ . '/../config.php';

if (empty($_SESSION['admin_login'])) {
    header('Location: index.php');
    exit;
}

try {
    $db = koneksi_database();
    $daftarPesan = $db->query(
        'SELECT id, nama, gmail, isi, tanggal_pengiriman_pesan FROM pesan ORDER BY id DESC'
    )->fetchAll();
} catch (Throwable $ex) {
    error_log('Gagal mengambil data pesan: ' . $ex->getMessage());
    http_response_code(500);
    exit('Gagal mengambil data pesan dari database.');
}

// Waktu disimpan dalam UTC, tampilkan sebagai WIB (UTC+7) seperti versi Python
function waktu_wib(?string $waktu): string
{
    if (!$waktu) {
        return '';
    }
    $dt = new DateTime($waktu, new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
    return $dt->format('Y-m-d H:i:s');
}

$token = token_csrf();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar border-bottom p-3">
        <div class="container">
            <span class="navbar-brand fw-bold">Portfolio Admin</span>

            <form action="logout.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                <button class="btn btn-outline-danger btn-sm" type="submit">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container py-5">
        <h1 class="h3 mb-4">Pesan dari Pengunjung</h1>

        <p>Total pesan: <strong><?= count($daftarPesan) ?></strong></p>

        <?php if ($daftarPesan): ?>
            <?php foreach ($daftarPesan as $pesan): ?>
                <div class="message-card border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <h2 class="h5"><?= e($pesan['nama']) ?></h2>
                        <small class="text-muted"><?= e(waktu_wib($pesan['tanggal_pengiriman_pesan'])) ?></small>
                    </div>

                    <p class="mb-2"><strong>Gmail:</strong> <?= e($pesan['gmail']) ?></p>

                    <p class="mb-3" style="white-space: pre-wrap;"><?= e($pesan['isi']) ?></p>

                    <form action="hapus.php?id=<?= (int) $pesan['id'] ?>" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                        <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm">Hapus Pesan</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">Belum ada pesan dari pengunjung.</div>
        <?php endif; ?>

        <a href="../contact.php" class="btn btn-secondary mt-3">Lihat Halaman Contact</a>
    </main>
</body>
</html>

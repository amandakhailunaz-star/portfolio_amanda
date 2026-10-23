<?php
declare(strict_types=1);
require __DIR__ . '/../config.php';
admin_required();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !periksa_csrf()) {
    http_response_code(400);
    exit('Permintaan tidak valid. Muat ulang halaman.');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('ID pesan tidak valid.');
}

try {
    $db = koneksi_database();
    $db->prepare('DELETE FROM pesan WHERE id = ?')->execute([$id]);
} catch (Throwable $ex) {
    error_log('Gagal menghapus pesan: ' . $ex->getMessage());
    http_response_code(500);
    exit('Pesan gagal dihapus.');
}

header('Location: dashboard.php');
exit;

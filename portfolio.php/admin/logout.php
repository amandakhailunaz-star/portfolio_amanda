<?php
declare(strict_types=1);
require __DIR__ . '/../config.php';
admin_required();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !periksa_csrf()) {
    http_response_code(400);
    exit('Permintaan tidak valid. Muat ulang halaman.');
}

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;

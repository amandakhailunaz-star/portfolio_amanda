<?php
declare(strict_types=1);

/**
 * Konfigurasi bersama: environment, session, database, CSRF, login admin.
 * Setara dengan bagian atas app.py (Flask).
 */

// Muat file .env sederhana (opsional) jika ada
$fileEnv = __DIR__ . '/.env';
if (is_readable($fileEnv)) {
    foreach (file($fileEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $baris) {
        $baris = trim($baris);
        if ($baris === '' || $baris[0] === '#' || strpos($baris, '=') === false) {
            continue;
        }
        [$k, $v] = explode('=', $baris, 2);
        $k = trim($k);
        $v = trim(trim($v), "\"'");
        if (getenv($k) === false) {
            putenv("$k=$v");
            $_ENV[$k] = $v;
        }
    }
}

function env(string $nama, ?string $default = null): ?string
{
    $nilai = getenv($nama);
    return ($nilai === false || $nilai === '') ? $default : $nilai;
}

// Login Admin
define('ADMIN_USERNAME', env('ADMIN_USERNAME', 'admin'));
define('ADMIN_PASSWORD', env('ADMIN_PASSWORD'));

if (!ADMIN_PASSWORD) {
    http_response_code(500);
    exit('Atur environment variable ADMIN_PASSWORD sebelum menjalankan aplikasi.');
}

// Session yang lebih aman
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();

// Koneksi database (Aiven MySQL)
function koneksi_database(): PDO
{
    $host = env('DB_HOST');
    $user = env('DB_USER');
    $pass = env('DB_PASSWORD');
    if (!$host || !$user || $pass === null) {
        throw new RuntimeException('Variabel DB_HOST, DB_USER, DB_PASSWORD belum diatur.');
    }

    $port = (int) env('DB_PORT', '20055');
    $nama = env('DB_NAME', 'defaultdb');

    $opsi = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 15,
    ];

    // SSL: gunakan CA dari DB_SSL_CA (unduh ca.pem dari Aiven) atau CA bawaan sistem
    $ca = env('DB_SSL_CA');
    if (!$ca) {
        foreach (['/etc/ssl/certs/ca-certificates.crt', '/etc/pki/tls/certs/ca-bundle.crt'] as $kandidat) {
            if (is_readable($kandidat)) {
                $ca = $kandidat;
                break;
            }
        }
    }
    if ($ca) {
        $opsi[PDO::MYSQL_ATTR_SSL_CA] = $ca;
        $opsi[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    }

    return new PDO(
        "mysql:host=$host;port=$port;dbname=$nama;charset=utf8mb4",
        $user,
        $pass,
        $opsi
    );
}

// Perlindungan CSRF sederhana
function token_csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function periksa_csrf(): bool
{
    $tokenForm    = (string) ($_POST['csrf_token'] ?? '');
    $tokenSession = (string) ($_SESSION['csrf_token'] ?? '');

    return $tokenSession !== '' && hash_equals($tokenSession, $tokenForm);
}

// Wajib login admin
function admin_required(): void
{
    if (empty($_SESSION['admin_login'])) {
        header('Location: index.php');
        exit;
    }
}

// Escape output HTML
function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

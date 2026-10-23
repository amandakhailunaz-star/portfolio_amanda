<?php
declare(strict_types=1);
require __DIR__ . '/../config.php';

if (!empty($_SESSION['admin_login'])) {
    header('Location: dashboard.php');
    exit;
}

$pesanError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!periksa_csrf()) {
        http_response_code(400);
        exit('Permintaan tidak valid. Muat ulang halaman login.');
    }

    $username = (string) ($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (hash_equals(ADMIN_USERNAME, $username) && hash_equals(ADMIN_PASSWORD, $password)) {
        session_regenerate_id(true);
        $_SESSION = ['admin_login' => true];
        token_csrf();
        header('Location: dashboard.php');
        exit;
    }

    $pesanError = 'Username atau password salah.';
}

$token = token_csrf();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8eef2; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; width: 100%; max-width: 400px; padding: 30px;
                      border-radius: 18px; box-shadow: 0 5px 25px #00000012; }
        .btn-pink { background: #d87599; color: white; }
        .btn-pink:hover { background: #c55e83; color: white; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="text-center mb-4">Admin Login</h2>

        <?php if ($pesanError !== ''): ?>
            <div class="alert alert-danger"><?= e($pesanError) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autocomplete="username">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-pink w-100">Login</button>
        </form>

        <p class="text-center mt-3 mb-0"><a href="../contact.php">Kembali ke Contact</a></p>
    </div>
</body>
</html>

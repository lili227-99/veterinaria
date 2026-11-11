<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
auth_start_session();

if (auth_is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$configError = '';
$next = $_GET['next'] ?? 'index.php';
if (!is_string($next) || !str_starts_with($next, '/') || str_starts_with($next, '//')) {
    $next = 'index.php';
}

try {
    $credentials = auth_credentials();
} catch (RuntimeException $exception) {
    $configError = $exception->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $configError === '') {
    $next = $_POST['next'] ?? 'index.php';
    if (!is_string($next) || !str_starts_with($next, '/') || str_starts_with($next, '//')) {
        $next = 'index.php';
    }

    if (!auth_valid_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'La sesión del formulario venció. Actualizá la página e intentá de nuevo.';
    } else {
        $username = trim((string) ($_POST['usuario'] ?? ''));
        $password = (string) ($_POST['clave'] ?? '');

        if (hash_equals((string) $credentials['username'], $username)
            && password_verify($password, (string) $credentials['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = $username;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: ' . $next);
            exit;
        }

        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Veterinaria</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <main class="login-card">
        <div class="login-icon" aria-hidden="true">🐾</div>
        <h1>Veterinaria</h1>
        <p class="login-subtitle">Ingresá a tu sistema de gestión</p>

        <?php if ($configError !== ''): ?>
            <p class="login-message error" role="alert"><?= htmlspecialchars($configError, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <p class="login-message error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="post" action="login.php" class="login-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>">

            <label for="usuario">Usuario</label>
            <input id="usuario" name="usuario" type="text" autocomplete="username" required autofocus>

            <label for="clave">Contraseña</label>
            <input id="clave" name="clave" type="password" autocomplete="current-password" required>

            <button type="submit" <?= $configError !== '' ? 'disabled' : '' ?>>Iniciar sesión</button>
        </form>
    </main>
</body>
</html>
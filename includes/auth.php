<?php

declare(strict_types=1);

function auth_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_name('veterinaria_session');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $https,
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function auth_is_logged_in(): bool
{
    auth_start_session();
    return isset($_SESSION['usuario']) && is_string($_SESSION['usuario']);
}

function auth_require_page(): void
{
    if (!auth_is_logged_in()) {
        $requested = $_SERVER['REQUEST_URI'] ?? 'index.php';
        header('Location: login.php?next=' . rawurlencode($requested));
        exit;
    }
}

function auth_require_json(): void
{
    if (!auth_is_logged_in()) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'mensaje' => 'Tu sesión expiró. Iniciá sesión nuevamente.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function auth_csrf_token(): string
{
    auth_start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function auth_valid_csrf(?string $token): bool
{
    auth_start_session();
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function auth_credentials(): array
{
    $path = __DIR__ . '/../config/auth.php';
    if (!is_file($path)) {
        throw new RuntimeException('Falta configurar config/auth.php. Seguí los pasos del README.');
    }

    $credentials = require $path;
    if (!is_array($credentials)
        || empty($credentials['username'])
        || empty($credentials['password_hash'])
        || !password_get_info((string) $credentials['password_hash'])['algo']) {
        throw new RuntimeException('La configuración de acceso no es válida. Revisá config/auth.php.');
    }

    return $credentials;
}

<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function login(string $email, string $password): bool
{
    $stmt = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
    return true;
}

function require_auth(?string $role = null): void
{
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    if ($role !== null && ($_SESSION['user']['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('Unauthorized');
    }
}

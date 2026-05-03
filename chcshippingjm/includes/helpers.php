<?php

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validate_csrf(string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

function generate_tracking_id(): string
{
    return 'CHC' . strtoupper(bin2hex(random_bytes(5)));
}

function send_email(string $to, string $subject, string $body): bool
{
    $headers = sprintf("From: %s <%s>\r\nContent-Type: text/plain; charset=UTF-8", SMTP_FROM_NAME, SMTP_FROM_EMAIL);
    $sent = @mail($to, $subject, $body, $headers);

    if (!$sent) {
        $line = sprintf("[%s] TO:%s SUBJECT:%s BODY:%s\n", date('c'), $to, $subject, $body);
        file_put_contents(MAIL_FALLBACK_LOG, $line, FILE_APPEND);
    }

    return $sent;
}

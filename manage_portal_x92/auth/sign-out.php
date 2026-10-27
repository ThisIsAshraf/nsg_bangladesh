<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';

// Log logout activity prior to session destruction
$admin = admin_user();
if ($admin) {
    log_activity('admin_logout', 'admin_users', (int)$admin['id']);
}

// Clear session variables
$_SESSION = [];

// Destroy session cookie if set
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $p['path'],
        $p['domain'] ?? '',
        $p['secure'],
        $p['httponly']
    );
}

// Destroy session completely
session_destroy();

// Redirect to masked admin sign-in route
header('Location: ' . site_url('admin/auth/sign-in.php'));
exit;

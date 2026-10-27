<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';

// Redirect to masked admin dashboard if already authenticated
if (admin_user()) {
    header('Location: ' . site_url('admin/'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $q = db()->prepare("
            SELECT u.id, u.role_id, u.full_name, u.email, u.password_hash, u.status, r.role_key, r.role_name 
            FROM admin_users u 
            JOIN admin_roles r ON r.id = u.role_id 
            WHERE u.email = ? LIMIT 1
        ");
        $q->execute([$email]);
        $u = $q->fetch();

        if ($u && password_verify($password, $u['password_hash'])) {
            if ($u['status'] !== 'active') {
                $error = 'Your account is currently disabled or locked.';
            } else {
                session_regenerate_id(true);

                $_SESSION['admin'] = [
                    'id' => (int)$u['id'],
                    'role_id' => (int)$u['role_id'],
                    'role_key' => $u['role_key'],
                    'role_name' => $u['role_name'],
                    'name' => $u['full_name'],
                    'full_name' => $u['full_name'],
                    'email' => $u['email']
                ];

                $update = db()->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?');
                $update->execute([$u['id']]);

                log_activity('admin_login', 'admin_users', (int)$u['id'], ['email' => $u['email']]);

                header('Location: ' . site_url('admin/'));
                exit;
            }
        } else {
            usleep(250000); // Mitigation against timing attacks
            $error = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Admin Sign In';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(setting('site_name', 'Noor Staffing')) ?></title>
    <link rel="stylesheet" href="<?= e(site_url('assets/style.css')) ?>">
    <?= render_dynamic_css() ?>
</head>

<body class="admin-auth-body">
    <section class="prose auth-container">
        <h1>Administrator Sign In</h1>

        <?php if ($error): ?>
            <p class="danger" style="color: #d9534f; background: #fdf7f7; padding: 0.75rem; border: 1px solid #d9534f; border-radius: 4px;">
                <?= e($error) ?>
            </p>
        <?php endif; ?>

        <form method="post" class="admin-form">
            <?= csrf_field() ?>

            <label class="field">
                <span>Email Address</span>
                <input type="email" name="email" required autocomplete="username" value="<?= e($_POST['email'] ?? '') ?>">
            </label>

            <label class="field">
                <span>Password</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>

            <p style="margin-top: 1.5rem;">
                <button type="submit" class="button">Sign In</button>
            </p>
        </form>

        <p class="muted" style="margin-top: 2rem; font-size: 0.875rem;">
            Protected access for authorized administrators only.
        </p>
    </section>
</body>

</html>
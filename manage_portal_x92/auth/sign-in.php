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
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body.admin-auth-body {
            background: #f4f6f8;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .auth-container {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid #eef2f5;
        }

        .auth-container h1 {
            font-size: 1.5rem;
            color: #123B5D;
            margin-bottom: 1.5rem;
            font-weight: 700;
            text-align: center;
        }

        .admin-form .field {
            display: block;
            margin-bottom: 1.25rem;
        }

        .admin-form .field span {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #444;
            margin-bottom: 0.4rem;
        }

        .admin-form input[type="email"],
        .admin-form input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: border-color 0.2s ease;
        }

        .admin-form input:focus {
            outline: none;
            border-color: #123B5D;
            box-shadow: 0 0 0 3px rgba(18, 59, 93, 0.1);
        }

        .button {
            width: 100%;
            padding: 0.85rem;
            background: #123B5D;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .button:hover {
            background: #0d2a43;
        }

        .danger {
            color: #c62828;
            background: #ffebee;
            padding: 0.75rem 1rem;
            border: 1px solid #ffcdd2;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
        }

        .muted {
            color: #777;
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.825rem;
        }
    </style>
</head>

<body class="admin-auth-body">
    <section class="auth-container">
        <h1>Administrator Sign In</h1>

        <?php if ($error): ?>
            <p class="danger">
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

        <p class="muted">
            Protected access for authorized administrators only.
        </p>
    </section>
</body>

</html>
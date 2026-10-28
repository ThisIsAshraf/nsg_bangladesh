<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('settings.write');

$message = '';
$error   = '';

// --- HELPER: Upload Logo File ---
function handle_logo_upload(): ?string
{
    if (!isset($_FILES['file_site_logo']) || $_FILES['file_site_logo']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = $_FILES['file_site_logo']['tmp_name'];
    $name    = basename($_FILES['file_site_logo']['name']);
    $ext     = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (!in_array($ext, $allowed, true)) {
        throw new Exception('Invalid logo format. Allowed: JPG, PNG, GIF, WEBP, SVG');
    }

    $uploadDir = dirname(__DIR__, 2) . '/uploads/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = 'site_logo_' . time() . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $targetPath)) {
        return 'uploads/images/' . $filename;
    }

    throw new Exception('Failed to upload logo image file.');
}

// Global Site & Branding Settings mapped to your header.php keys
$settingKeys = [
    // Branding & Logo
    'site_logo'               => 'Site Logo Relative Path',
    'logo_width'              => 'Logo Width in Pixels (e.g., 180)',
    'site_name'               => 'Site Name (English)',
    'site_name_bn'            => 'Site Name (Bangla)',
    'seo_description'         => 'Default Meta Description',

    // Contact Information
    'contact_email'           => 'Primary Contact Email Address',
    'contact_phone'           => 'Primary Phone / Mobile Number',
    'contact_address'         => 'Bangladesh Office Address (English)',
    'head_office_address'     => 'US Head Office Address (English)',

    // Social Media Links (Matches setting('linkedin_url') in header.php)
    'linkedin_url'            => 'LinkedIn Profile URL',
    'facebook_url'            => 'Facebook Page URL',
    'instagram_url'           => 'Instagram Profile URL',

    // Theme Colors
    'theme_primary_color'     => 'Primary Brand Color (Hex)',
    'theme_accent_color'      => 'Accent / Red Highlight Color (Hex)'
];

// --- POST HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        db()->beginTransaction();

        $stmt = db()->prepare('
            INSERT INTO settings (`key`, `value`, `updated_at`) 
            VALUES (?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW()
        ');

        $uploadedLogo = handle_logo_upload();

        foreach (array_keys($settingKeys) as $key) {
            $val = '';
            if ($key === 'site_logo' && $uploadedLogo !== null) {
                $val = $uploadedLogo;
            } else {
                $val = trim((string)($_POST['setting_' . $key] ?? ''));
            }
            $stmt->execute([$key, $val]);
        }

        db()->commit();
        $message = 'Global site settings and logo branding saved successfully!';
    } catch (Throwable $t) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        $error = 'Save failed: ' . $t->getMessage();
    }
}

// Fetch all current settings
$currentSettings = [];
try {
    $rows = db()->query('SELECT `key`, `value` FROM settings')->fetchAll();
    foreach ($rows as $r) {
        $currentSettings[$r['key']] = $r['value'];
    }
} catch (Throwable $t) {
}

$pageTitle = 'Site Settings & Theme Configuration';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body {
            background: #f4f6f8;
            color: #333;
            line-height: 1.5;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .admin-sidebar {
            width: 260px;
            min-width: 260px;
            background: #123B5D;
            color: #fff;
            padding: 1.5rem 1rem;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }

        .admin-sidebar h2 {
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            padding-bottom: 0.75rem;
        }

        .admin-sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            flex: 1;
        }

        .admin-sidebar-nav a {
            color: #cfd8dc;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            display: block;
        }

        .admin-sidebar-nav a:hover,
        .admin-sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-weight: 600;
        }

        .admin-main {
            flex: 1;
            padding: 2rem;
            background: #f4f6f8;
            overflow-y: auto;
            max-width: 100%;
        }

        .card-panel {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            border: 1px solid #eef2f5;
        }

        .card-panel h2 {
            font-size: 1.25rem;
            color: #123B5D;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.65rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.95rem;
        }

        .color-picker-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .color-picker-wrap input[type="color"] {
            width: 50px;
            height: 38px;
            cursor: pointer;
            padding: 2px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-submit {
            padding: 0.85rem 2rem;
            background: #123B5D;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="admin-layout">
        <?= render_admin_sidebar('settings') ?>

        <main class="admin-main">
            <h1>Global Settings & Theme (`settings.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Manage site logo, logo width, navigation branding, contact info, social profiles, and theme colors.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <form method="post" action="settings.php" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- LOGO & BRANDING -->
                <div class="card-panel">
                    <h2>🖼️ Brand Logo & Width Controls</h2>

                    <div class="form-group">
                        <label><?= $settingKeys['site_logo'] ?></label>
                        <input type="text" name="setting_site_logo" id="setting_site_logo" value="<?= e($currentSettings['site_logo'] ?? 'assets/logo.png') ?>">
                        <label style="font-size:0.8rem; font-weight:normal; margin-top:0.4rem; color:#666;">Or upload new logo file:</label>
                        <input type="file" name="file_site_logo" accept="image/*">
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label><?= $settingKeys['logo_width'] ?></label>
                            <input type="number" name="setting_logo_width" value="<?= e($currentSettings['logo_width'] ?? '180') ?>" placeholder="180">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['site_name'] ?></label>
                            <input type="text" name="setting_site_name" value="<?= e($currentSettings['site_name'] ?? 'Noor Staffing Group') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['site_name_bn'] ?></label>
                            <input type="text" name="setting_site_name_bn" value="<?= e($currentSettings['site_name_bn'] ?? 'নূর স্টাফিং গ্রুপ') ?>">
                        </div>
                    </div>
                </div>

                <!-- SOCIAL & CONTACT -->
                <div class="card-panel">
                    <h2>🔗 Social Media & Contact Info</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label><?= $settingKeys['linkedin_url'] ?></label>
                            <input type="url" name="setting_linkedin_url" value="<?= e($currentSettings['linkedin_url'] ?? '#') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['facebook_url'] ?></label>
                            <input type="url" name="setting_facebook_url" value="<?= e($currentSettings['facebook_url'] ?? '#') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['instagram_url'] ?></label>
                            <input type="url" name="setting_instagram_url" value="<?= e($currentSettings['instagram_url'] ?? '#') ?>">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label><?= $settingKeys['contact_email'] ?></label>
                            <input type="email" name="setting_contact_email" value="<?= e($currentSettings['contact_email'] ?? 'contact@noorstaffing.com') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['contact_phone'] ?></label>
                            <input type="text" name="setting_contact_phone" value="<?= e($currentSettings['contact_phone'] ?? '+880 1700-000000') ?>">
                        </div>
                    </div>
                </div>

                <!-- THEME COLORS -->
                <div class="card-panel">
                    <h2>🎨 Theme Color Variables</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label><?= $settingKeys['theme_primary_color'] ?></label>
                            <div class="color-picker-wrap">
                                <input type="color" value="<?= e($currentSettings['theme_primary_color'] ?? '#123B5D') ?>" onchange="document.getElementById('setting_theme_primary_color').value = this.value">
                                <input type="text" name="setting_theme_primary_color" id="setting_theme_primary_color" value="<?= e($currentSettings['theme_primary_color'] ?? '#123B5D') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['theme_accent_color'] ?></label>
                            <div class="color-picker-wrap">
                                <input type="color" value="<?= e($currentSettings['theme_accent_color'] ?? '#B71C1C') ?>" onchange="document.getElementById('setting_theme_accent_color').value = this.value">
                                <input type="text" name="setting_theme_accent_color" id="setting_theme_accent_color" value="<?= e($currentSettings['theme_accent_color'] ?? '#B71C1C') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Save Settings & Branding</button>
            </form>
        </main>
    </div>

</body>

</html>
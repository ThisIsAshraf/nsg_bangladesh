<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('settings.write');

$message = '';
$error   = '';

// Comprehensive list of global site settings
$settingKeys = [
    // 1. General Site Identity
    'site_name'             => 'Site Name (English)',
    'site_name_bn'          => 'Site Name (Bangla)',
    'site_tagline'          => 'Site Tagline / Subtitle',
    'site_logo_text'        => 'Header Brand Text / Logo Title',

    // 2. Contact Information
    'contact_email'         => 'Primary Contact Email Address',
    'contact_phone'         => 'Primary Phone / Mobile Number',
    'contact_address'       => 'Bangladesh Office Address (English)',
    'contact_address_bn'    => 'Bangladesh Office Address (Bangla)',
    'head_office_address'   => 'US Head Office Address (English)',
    'head_office_address_bn' => 'US Head Office Address (Bangla)',
    'contact_map_embed_url' => 'Google Maps Embed URL (iframe src)',

    // 3. Social Media Links
    'social_facebook'       => 'Facebook URL',
    'social_linkedin'       => 'LinkedIn URL',
    'social_twitter'        => 'Twitter / X URL',
    'social_instagram'      => 'Instagram URL',

    // 4. Default SEO Fallbacks
    'default_seo_title'       => 'Default Browser Title',
    'default_seo_description' => 'Default Meta Description',

    // 5. Global Theme Colors
    'theme_primary_color'   => 'Primary Brand Color (Hex)',
    'theme_accent_color'    => 'Accent Color (Hex)'
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

        foreach (array_keys($settingKeys) as $key) {
            $val = trim((string)($_POST['setting_' . $key] ?? ''));
            $stmt->execute([$key, $val]);
        }

        db()->commit();
        $message = 'Global settings and theme variables updated successfully!';
    } catch (Throwable $t) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        $error = 'Save failed: ' . $t->getMessage();
    }
}

// Fetch all current setting values from DB
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
            <p style="color: #666; margin-bottom: 1.5rem;">Manage site identity, global contact info, office locations, social profiles, and brand theme colors.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <form method="post" action="settings.php">
                <?= csrf_field() ?>

                <!-- MODULE 1: SITE IDENTITY -->
                <div class="card-panel">
                    <h2>🏢 Site Identity & Branding</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label><?= $settingKeys['site_name'] ?></label>
                            <input type="text" name="setting_site_name" value="<?= e($currentSettings['site_name'] ?? 'Noor Staffing Group') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['site_name_bn'] ?></label>
                            <input type="text" name="setting_site_name_bn" value="<?= e($currentSettings['site_name_bn'] ?? 'নূর স্টাফিং গ্রুপ') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['site_tagline'] ?></label>
                            <input type="text" name="setting_site_tagline" value="<?= e($currentSettings['site_tagline'] ?? 'Offshore Operational Excellence') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['site_logo_text'] ?></label>
                            <input type="text" name="setting_site_logo_text" value="<?= e($currentSettings['site_logo_text'] ?? 'NOOR STAFFING') ?>">
                        </div>
                    </div>
                </div>

                <!-- MODULE 2: CONTACT DETAILS & LOCATIONS -->
                <div class="card-panel" style="border-top: 4px solid #b71c1c;">
                    <h2>📍 Global Contact & Office Locations</h2>
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

                    <div class="form-grid">
                        <div class="form-group">
                            <label><?= $settingKeys['contact_address'] ?></label>
                            <textarea name="setting_contact_address" rows="2"><?= e($currentSettings['contact_address'] ?? 'Dhaka, Bangladesh') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['contact_address_bn'] ?></label>
                            <textarea name="setting_contact_address_bn" rows="2"><?= e($currentSettings['contact_address_bn'] ?? 'ঢাকা, বাংলাদেশ') ?></textarea>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label><?= $settingKeys['head_office_address'] ?></label>
                            <textarea name="setting_head_office_address" rows="2"><?= e($currentSettings['head_office_address'] ?? 'New York City, NY, USA') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['head_office_address_bn'] ?></label>
                            <textarea name="setting_head_office_address_bn" rows="2"><?= e($currentSettings['head_office_address_bn'] ?? 'নিউ ইয়র্ক সিটি, নিউ ইয়র্ক, যুক্তরাষ্ট্র') ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $settingKeys['contact_map_embed_url'] ?></label>
                        <input type="text" name="setting_contact_map_embed_url" value="<?= e($currentSettings['contact_map_embed_url'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                    </div>
                </div>

                <!-- MODULE 3: SOCIAL MEDIA LINKS -->
                <div class="card-panel">
                    <h2>🔗 Social Media Links</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label><?= $settingKeys['social_linkedin'] ?></label>
                            <input type="url" name="setting_social_linkedin" value="<?= e($currentSettings['social_linkedin'] ?? 'https://linkedin.com') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['social_facebook'] ?></label>
                            <input type="url" name="setting_social_facebook" value="<?= e($currentSettings['social_facebook'] ?? 'https://facebook.com') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['social_twitter'] ?></label>
                            <input type="url" name="setting_social_twitter" value="<?= e($currentSettings['social_twitter'] ?? 'https://twitter.com') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $settingKeys['social_instagram'] ?></label>
                            <input type="url" name="setting_social_instagram" value="<?= e($currentSettings['social_instagram'] ?? 'https://instagram.com') ?>">
                        </div>
                    </div>
                </div>

                <!-- MODULE 4: DEFAULT SEO FALLBACKS & THEME COLORS -->
                <div class="card-panel">
                    <h2>🔍 Default SEO & Brand Theme Colors</h2>
                    <div class="form-group">
                        <label><?= $settingKeys['default_seo_title'] ?></label>
                        <input type="text" name="setting_default_seo_title" value="<?= e($currentSettings['default_seo_title'] ?? 'Noor Staffing Group | Offshore Workforce Solutions') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= $settingKeys['default_seo_description'] ?></label>
                        <textarea name="setting_default_seo_description" rows="2"><?= e($currentSettings['default_seo_description'] ?? 'Connecting global businesses with high-value placement and staffing solutions in Bangladesh.') ?></textarea>
                    </div>

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

                <button type="submit" class="btn-submit">Save All Global Settings</button>
            </form>
        </main>
    </div>

</body>

</html>
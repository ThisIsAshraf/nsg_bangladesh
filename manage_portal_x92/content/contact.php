<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('pages.read');

$pageSlug = 'contact';
$message  = '';
$error    = '';

// --- POST HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['form_action'] ?? 'save_contact_settings';

    try {
        if ($action === 'save_contact_settings') {
            db()->beginTransaction();

            // 1. Save Page & SEO Meta
            $qPage = db()->prepare('
                INSERT INTO pages (title, slug, seo_title, seo_description, status, created_at, updated_at, updated_by) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?) 
                ON DUPLICATE KEY UPDATE 
                    seo_title = VALUES(seo_title), 
                    seo_description = VALUES(seo_description), 
                    status = VALUES(status), 
                    updated_at = NOW(), 
                    updated_by = VALUES(updated_by)
            ');
            $qPage->execute([
                'Contact Page',
                $pageSlug,
                trim($_POST['seo_title'] ?? ''),
                trim($_POST['seo_description'] ?? ''),
                $_POST['status'] ?? 'published',
                $admin['id']
            ]);

            // 2. Save Page Sections
            $qSection = db()->prepare('
                INSERT INTO page_sections (page_slug, section_key, content_type, content_value) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
            ');

            $contactFields = [
                'contact_hero_title'       => $_POST['sec_contact_hero_title'] ?? 'Get In Touch',
                'contact_hero_title_color' => $_POST['sec_contact_hero_title_color'] ?? '#111111',
                'contact_hero_title_size'  => $_POST['sec_contact_hero_title_size'] ?? '3rem',
                'contact_intro'            => $_POST['sec_contact_intro'] ?? '',
                'contact_intro_color'      => $_POST['sec_contact_intro_color'] ?? '#555555',
                'contact_intro_size'       => $_POST['sec_contact_intro_size'] ?? '1.15rem',
                'contact_success_msg'      => $_POST['sec_contact_success_msg'] ?? ''
            ];

            foreach ($contactFields as $k => $v) {
                $type = str_contains($k, 'color') ? 'color' : (str_contains($k, 'intro') ? 'textarea' : 'text');
                $qSection->execute([$pageSlug, $k, $type, trim((string)$v)]);
            }

            // 3. Save Office Locations & Contact Settings in `settings` table
            $qSetting = db()->prepare('
                INSERT INTO settings (`key`, `value`, `updated_at`) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW()
            ');

            $siteSettings = [
                'contact_email'         => trim($_POST['setting_contact_email'] ?? ''),
                'contact_phone'         => trim($_POST['setting_contact_phone'] ?? ''),
                'contact_address'       => trim($_POST['setting_contact_address'] ?? ''),
                'contact_address_bn'    => trim($_POST['setting_contact_address_bn'] ?? ''),
                'head_office_address'   => trim($_POST['setting_head_office_address'] ?? ''),
                'head_office_address_bn' => trim($_POST['setting_head_office_address_bn'] ?? ''),
                'contact_map_embed_url' => trim($_POST['setting_contact_map_embed_url'] ?? '')
            ];

            foreach ($siteSettings as $sk => $sv) {
                $qSetting->execute([$sk, $sv]);
            }

            db()->commit();
            $message = 'Contact page, office addresses, and map embed URL saved successfully!';
        }

        // Action: Mark Message Read / Delete
        elseif ($action === 'delete_message') {
            $msgId = (int)($_POST['msg_id'] ?? 0);
            if ($msgId > 0) {
                db()->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$msgId]);
                $message = 'Contact inquiry deleted successfully!';
            }
        }
    } catch (Throwable $t) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        $error = 'Save failed: ' . $t->getMessage();
    }
}

// Fetch Page Record & Sections
$pageRecord = null;
try {
    $q = db()->prepare('SELECT * FROM pages WHERE slug = ? LIMIT 1');
    $q->execute([$pageSlug]);
    $pageRecord = $q->fetch();
} catch (Throwable $t) {
}

$savedValues = [];
try {
    $q = db()->prepare('SELECT section_key, content_value FROM page_sections WHERE page_slug = ?');
    $q->execute([$pageSlug]);
    $rows = $q->fetchAll();
    foreach ($rows as $r) {
        $savedValues[$r['section_key']] = $r['content_value'];
    }
} catch (Throwable $t) {
}

// Fetch Contact Messages List
$messagesList = [];
try {
    $messagesList = db()->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 20")->fetchAll();
} catch (Throwable $t) {
}

$pageTitle = 'Manage Contact Page';
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
            margin-bottom: 1.5rem;
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

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .item-table th,
        .item-table td {
            padding: 0.85rem 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }

        .item-table th {
            background: #fafafa;
            font-weight: 600;
            color: #555;
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
        <?= render_admin_sidebar('contact_pg') ?>

        <main class="admin-main">
            <h1>Manage Contact Page (`contact.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Manage office addresses, phone numbers, email, US headquarters location, Google Map embed URL, and contact form header text.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <form method="post" action="contact.php">
                <?= csrf_field() ?>
                <input type="hidden" name="form_action" value="save_contact_settings">

                <!-- MODULE 1: LOCAL & GLOBAL OFFICE ADDRESSES -->
                <div class="card-panel" style="border-top: 4px solid #b71c1c;">
                    <h2>📍 Office Locations & Contact Details</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Contact Email Address *</label>
                            <input type="email" name="setting_contact_email" value="<?= e(setting('contact_email', 'contact@noorstaffing.com')) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Contact Phone Number *</label>
                            <input type="text" name="setting_contact_phone" value="<?= e(setting('contact_phone', '+880 1700-000000')) ?>" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Bangladesh Office Address (English)</label>
                            <textarea name="setting_contact_address" rows="2"><?= e(setting('contact_address', 'Dhaka, Bangladesh')) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Bangladesh Office Address (Bangla)</label>
                            <textarea name="setting_contact_address_bn" rows="2"><?= e(setting('contact_address_bn', 'ঢাকা, বাংলাদেশ')) ?></textarea>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>US Head Office Address (English)</label>
                            <textarea name="setting_head_office_address" rows="2"><?= e(setting('head_office_address', 'New York City, NY, USA')) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>US Head Office Address (Bangla)</label>
                            <textarea name="setting_head_office_address_bn" rows="2"><?= e(setting('head_office_address_bn', 'নিউ ইয়র্ক সিটি, নিউ ইয়র্ক, যুক্তরাষ্ট্র')) ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Google Maps Embed URL (`src` attribute from Google Maps iframe)</label>
                        <input type="text" name="setting_contact_map_embed_url" value="<?= e(setting('contact_map_embed_url', '')) ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                    </div>
                </div>

                <!-- MODULE 2: HERO & FORM COPY -->
                <div class="card-panel">
                    <h2>📝 Page Titles & Form Copy</h2>

                    <div class="form-group">
                        <label>Contact Page Main Title</label>
                        <input type="text" name="sec_contact_hero_title" value="<?= e($savedValues['contact_hero_title'] ?? 'Get In Touch') ?>">
                    </div>

                    <div class="form-group">
                        <label>Title Color</label>
                        <div class="color-picker-wrap">
                            <input type="color" value="<?= e($savedValues['contact_hero_title_color'] ?? '#111111') ?>" onchange="document.getElementById('sec_contact_hero_title_color').value = this.value">
                            <input type="text" name="sec_contact_hero_title_color" id="sec_contact_hero_title_color" value="<?= e($savedValues['contact_hero_title_color'] ?? '#111111') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Form Instructions / Subtitle Copy</label>
                        <textarea name="sec_contact_intro" rows="2"><?= e($savedValues['contact_intro'] ?? 'Reach out for business inquiries...') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Form Submission Success Message</label>
                        <input type="text" name="sec_contact_success_msg" value="<?= e($savedValues['contact_success_msg'] ?? 'Thank you! Your message has been sent successfully.') ?>">
                    </div>

                    <div class="form-group">
                        <label>SEO Browser Title</label>
                        <input type="text" name="seo_title" value="<?= e($pageRecord['seo_title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>SEO Meta Description</label>
                        <textarea name="seo_description" rows="2"><?= e($pageRecord['seo_description'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Save Contact Page Settings</button>
                </div>
            </form>

            <!-- MODULE 3: RECENT CONTACT INQUIRIES LIST -->
            <div class="card-panel">
                <h2>📥 Recent Submitted Inquiries (<?= count($messagesList) ?>)</h2>
                <?php if (empty($messagesList)): ?>
                    <p style="color:#888; font-size:0.9rem;">No inquiries submitted yet.</p>
                <?php else: ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messagesList as $msg): ?>
                                <tr>
                                    <td><small><?= e(date('M d, Y H:i', strtotime($msg['created_at']))) ?></small></td>
                                    <td><strong><?= e($msg['name']) ?></strong></td>
                                    <td><a href="mailto:<?= e($msg['email']) ?>" style="color:#123B5D;"><?= e($msg['email']) ?></a></td>
                                    <td><?= e($msg['subject'] ?? 'General Inquiry') ?></td>
                                    <td><?= e(substr($msg['message'], 0, 60)) ?>...</td>
                                    <td>
                                        <form method="post" action="contact.php" onsubmit="return confirm('Delete message?');" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_message">
                                            <input type="hidden" name="msg_id" value="<?= (int)$msg['id'] ?>">
                                            <button type="submit" style="background:none; border:none; color:#c62828; cursor:pointer; font-weight:600;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>

</body>

</html>
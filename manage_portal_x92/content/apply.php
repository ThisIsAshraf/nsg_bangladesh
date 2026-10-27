<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('pages.read');

$pageSlug = 'apply';
$message  = '';
$error    = '';

$applyFields = [
    'apply_hero_title'       => ['label' => 'Application Form Header Title', 'type' => 'text', 'default' => 'Submit Your Application'],
    'apply_hero_title_color' => ['label' => 'Header Text Color', 'type' => 'color', 'default' => '#111111'],
    'apply_hero_title_size'  => ['label' => 'Header Font Size', 'type' => 'text', 'default' => '2.75rem'],
    'apply_intro'            => ['label' => 'Form Instructions Copy', 'type' => 'textarea', 'default' => 'Please upload your latest CV/Resume (PDF or DOCX) and fill out the details below.'],
    'apply_intro_color'      => ['label' => 'Instructions Text Color', 'type' => 'color', 'default' => '#555555'],
    'apply_intro_size'       => ['label' => 'Instructions Font Size', 'type' => 'text', 'default' => '1.1rem'],
    'apply_success_msg'      => ['label' => 'Application Confirmation Success Text', 'type' => 'text', 'default' => 'Application submitted successfully! Our talent acquisition team will review your CV.']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        db()->beginTransaction();

        $qPage = db()->prepare('
            INSERT INTO pages (title, slug, seo_title, seo_description, status, created_at, updated_at, updated_by) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?) 
            ON DUPLICATE KEY UPDATE seo_title = VALUES(seo_title), seo_description = VALUES(seo_description), status = VALUES(status), updated_at = NOW(), updated_by = VALUES(updated_by)
        ');
        $qPage->execute(['Apply Page', $pageSlug, trim($_POST['seo_title'] ?? ''), trim($_POST['seo_description'] ?? ''), $_POST['status'] ?? 'published', $admin['id']]);

        $qSection = db()->prepare('
            INSERT INTO page_sections (page_slug, section_key, content_type, content_value) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
        ');

        foreach ($applyFields as $secKey => $secConf) {
            $val = trim((string)($_POST['sec_' . $secKey] ?? ''));
            $qSection->execute([$pageSlug, $secKey, $secConf['type'], $val]);
        }

        db()->commit();
        $message = 'Application page text and settings saved successfully!';
    } catch (Throwable $t) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        $error = 'Save failed: ' . $t->getMessage();
    }
}

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

$pageTitle = 'Manage Candidate Application Page';
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
    </style>
</head>

<body>

    <div class="admin-layout">
        <?= render_admin_sidebar('apply_pg') ?>

        <main class="admin-main">
            <h1>Manage Candidate Application Page (`apply.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Control candidate submission form copy, titles, and confirmation text.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <form method="post" action="apply.php">
                <?= csrf_field() ?>

                <div class="card-panel">
                    <h2>📝 Application Form Headers & Text Copy</h2>
                    <?php foreach ($applyFields as $key => $conf): ?>
                        <?php $val = $savedValues[$key] ?? $conf['default']; ?>
                        <div class="form-group">
                            <label for="sec_<?= e($key) ?>"><?= e($conf['label']) ?></label>
                            <?php if ($conf['type'] === 'textarea'): ?>
                                <textarea name="sec_<?= e($key) ?>" id="sec_<?= e($key) ?>" rows="3"><?= e($val) ?></textarea>
                            <?php elseif ($conf['type'] === 'color'): ?>
                                <div class="color-picker-wrap">
                                    <input type="color" value="<?= e($val) ?>" onchange="document.getElementById('sec_<?= e($key) ?>').value = this.value">
                                    <input type="text" name="sec_<?= e($key) ?>" id="sec_<?= e($key) ?>" value="<?= e($val) ?>">
                                </div>
                            <?php else: ?>
                                <input type="text" name="sec_<?= e($key) ?>" id="sec_<?= e($key) ?>" value="<?= e($val) ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card-panel">
                    <h2>🔍 SEO Metadata</h2>
                    <div class="form-group">
                        <label for="seo_title">Browser Title</label>
                        <input type="text" name="seo_title" id="seo_title" value="<?= e($pageRecord['seo_title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="seo_description">Meta Description</label>
                        <textarea name="seo_description" id="seo_description" rows="2"><?= e($pageRecord['seo_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Publication Status</label>
                        <select name="status" id="status">
                            <option value="published" <?= ($pageRecord['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= ($pageRecord['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Save Application Page Settings</button>
            </form>
        </main>
    </div>

</body>

</html>
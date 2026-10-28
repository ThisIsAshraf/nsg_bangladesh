<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('pages.read');

$pageSlug = 'index';
$message  = '';
$error    = '';

// --- HELPER: Upload Home Feature Images ---
function handle_home_image_upload(string $fileInputKey): ?string
{
    if (!isset($_FILES[$fileInputKey]) || $_FILES[$fileInputKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = $_FILES[$fileInputKey]['tmp_name'];
    $name    = basename($_FILES[$fileInputKey]['name']);
    $ext     = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (!in_array($ext, $allowed, true)) {
        throw new Exception('Invalid file format. Allowed: JPG, PNG, GIF, WEBP, SVG');
    }

    $uploadDir = dirname(__DIR__, 2) . '/uploads/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('home_img_', true) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $targetPath)) {
        return 'uploads/images/' . $filename;
    }

    throw new Exception('Failed to upload image file.');
}

// Complete Fields Configuration including Leadership Overrides
$homeFields = [
    // 1. Split Hero Section
    'hero_title'          => ['label' => 'Hero Main Heading Title', 'type' => 'text', 'default' => 'Operational excellence with global impact'],
    'hero_title_color'    => ['label' => 'Hero Heading Text Color', 'type' => 'color', 'default' => '#ffffff'],
    'hero_title_size'     => ['label' => 'Hero Heading Font Size', 'type' => 'text', 'default' => '3.5rem'],
    'hero_image'          => ['label' => 'Hero Left Side Image Path', 'type' => 'image', 'default' => 'assets/hero-city.jpg'],

    // 2. About Section
    'home_about_heading'  => ['label' => 'About Section Heading', 'type' => 'text', 'default' => 'Noor Staffing Group in Bangladesh'],
    'home_about_body'     => ['label' => 'About Section Paragraph Text', 'type' => 'textarea', 'default' => 'Noor Staffing Group, part of the Noor, Inc. group of companies, supports its nationwide US staffing and workforce solutions through a dedicated offshore support team based in Bangladesh.'],
    'about_section_image' => ['label' => 'About Section Side Image Path', 'type' => 'image', 'default' => 'assets/dhaka-city.jpg'],

    // 3. CTA Banner
    'cta_heading'         => ['label' => 'CTA Banner Title Text', 'type' => 'text', 'default' => 'Take the first step toward your dreams.'],
    'cta_button_label'    => ['label' => 'CTA Button Text', 'type' => 'text', 'default' => 'SUBMIT YOUR RESUME'],

    // 4. Leadership Highlight (Featured Leader Override)
    'leader_name'         => ['label' => 'Leader Full Name', 'type' => 'text', 'default' => 'Mr. Noor'],
    'leader_photo'        => ['label' => 'Leader Photo / Headshot', 'type' => 'image', 'default' => 'assets/leader-placeholder.jpg'],
    'leader_speech'       => ['label' => 'Leader Speech / Biography Copy', 'type' => 'textarea', 'default' => 'Since 2003, Mr. Noor has been the individual owner and president of Noor Inc., a privately held umbrella company headquartered in New York City.'],

    // 5. NSG at a Glance Stats (1-5)
    'stat_1'              => ['label' => 'At a Glance Stat 1', 'type' => 'text', 'default' => '20+ Years in operation'],
    'stat_2'              => ['label' => 'At a Glance Stat 2', 'type' => 'text', 'default' => '14 US offices supporting clients in 46 states'],
    'stat_3'              => ['label' => 'At a Glance Stat 3', 'type' => 'text', 'default' => '250+ corporate staff in the U.S. and 100+ team members in Bangladesh'],
    'stat_4'              => ['label' => 'At a Glance Stat 4', 'type' => 'text', 'default' => '22 specialized brands under the Noor, Inc. portfolio'],
    'stat_5'              => ['label' => 'At a Glance Stat 5', 'type' => 'text', 'default' => '6,000+ contingent workers actively employed nationwide'],

    // 6. Vision Banner
    'vision_quote'        => ['label' => 'Vision Statement Quote Text', 'type' => 'text', 'default' => 'Driven by talent and built on trust.'],
    'vision_bg_image'     => ['label' => 'Vision Section Background Image', 'type' => 'image', 'default' => 'assets/bd-flag-bg.jpg'],

    // 7. Our Expertise Pillars (4 Cards)
    'exp_1_icon'          => ['label' => 'Expertise 1 Icon/Emoji', 'type' => 'text', 'default' => '👥'],
    'exp_1_title'         => ['label' => 'Expertise 1 Title', 'type' => 'text', 'default' => 'Staffing Solutions'],
    'exp_1_body'          => ['label' => 'Expertise 1 Description', 'type' => 'textarea', 'default' => 'Providing fast, reliable staffing support by connecting businesses with pre-vetted talent.'],

    'exp_2_icon'          => ['label' => 'Expertise 2 Icon/Emoji', 'type' => 'text', 'default' => '📄'],
    'exp_2_title'         => ['label' => 'Expertise 2 Title', 'type' => 'text', 'default' => 'Recruitment Services'],
    'exp_2_body'          => ['label' => 'Expertise 2 Description', 'type' => 'textarea', 'default' => 'Our experienced recruiters partner closely with clients to identify and hire top-tier professionals.'],

    'exp_3_icon'          => ['label' => 'Expertise 3 Icon/Emoji', 'type' => 'text', 'default' => '🧮'],
    'exp_3_title'         => ['label' => 'Expertise 3 Title', 'type' => 'text', 'default' => 'Payroll & Employer Services'],
    'exp_3_body'          => ['label' => 'Expertise 3 Description', 'type' => 'textarea', 'default' => 'Handling complex payroll processes to ensure full compliance, reduce risk, and simplify administration.'],

    'exp_4_icon'          => ['label' => 'Expertise 4 Icon/Emoji', 'type' => 'text', 'default' => '⚙️'],
    'exp_4_title'         => ['label' => 'Expertise 4 Title', 'type' => 'text', 'default' => 'Workforce Management'],
    'exp_4_body'          => ['label' => 'Expertise 4 Description', 'type' => 'textarea', 'default' => 'Offering comprehensive workforce solutions that include oversight, compliance, and transparent reporting.'],

    // 8. Culture & Office Section
    'culture_heading'     => ['label' => 'Culture Section Heading', 'type' => 'text', 'default' => 'Opportunities with NSG Bangladesh'],
    'culture_body'        => ['label' => 'Culture Section Description Paragraph', 'type' => 'textarea', 'default' => 'At NSG Bangladesh, we are building more than just an offshore support team. We are creating a place where talented professionals can grow, contribute, and be part of something bigger.'],
    'office_wall_image'   => ['label' => 'Office Showcase Image Path', 'type' => 'image', 'default' => 'assets/office-wall.png']
];

// --- POST HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        db()->beginTransaction();

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
            'Home Page',
            $pageSlug,
            trim($_POST['seo_title'] ?? ''),
            trim($_POST['seo_description'] ?? ''),
            $_POST['status'] ?? 'published',
            $admin['id']
        ]);

        $qSection = db()->prepare('
            INSERT INTO page_sections (page_slug, section_key, content_type, content_value) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
        ');

        foreach ($homeFields as $secKey => $secConf) {
            $val = '';
            if ($secConf['type'] === 'image') {
                $uploaded = handle_home_image_upload('file_' . $secKey);
                $val = $uploaded !== null ? $uploaded : trim((string)($_POST['sec_' . $secKey] ?? ''));
            } else {
                $val = trim((string)($_POST['sec_' . $secKey] ?? ''));
            }

            $qSection->execute([$pageSlug, $secKey, $secConf['type'], $val]);
        }

        db()->commit();
        $message = 'Home page settings and leadership section updated successfully!';
    } catch (Throwable $t) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        $error = 'Save failed: ' . $t->getMessage();
    }
}

// Fetch Saved Page Data
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

$pageTitle = 'Manage Home Page';
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

        .btn-submit:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="admin-layout">
        <?= render_admin_sidebar('home_pg') ?>

        <main class="admin-main">
            <h1>Manage Home Page (`index.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Control hero taglines, feature images, leader headshot/speech, stats metrics, expertise pillars, and vision section quotes.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <form method="post" action="home.php" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="card-panel">
                    <h2>🏠 Hero, Leadership & Page Sections</h2>
                    <?php foreach ($homeFields as $key => $conf): ?>
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
                            <?php elseif ($conf['type'] === 'image'): ?>
                                <input type="text" name="sec_<?= e($key) ?>" id="sec_<?= e($key) ?>" value="<?= e($val) ?>" placeholder="assets/example.jpg">
                                <label style="font-size:0.8rem; font-weight:normal; margin-top:0.4rem; color:#666;">Or upload new image file:</label>
                                <input type="file" name="file_<?= e($key) ?>" accept="image/*">
                            <?php else: ?>
                                <input type="text" name="sec_<?= e($key) ?>" id="sec_<?= e($key) ?>" value="<?= e($val) ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card-panel">
                    <h2>🔍 SEO Metadata & Publication Status</h2>
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

                <button type="submit" class="btn-submit">Save Home Page Settings</button>
            </form>
        </main>
    </div>

</body>

</html>
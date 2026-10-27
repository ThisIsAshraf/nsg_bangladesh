<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('pages.read');

$pageSlug = 'about';
$message  = '';
$error    = '';

// --- HELPER: Upload Media Assets ---
function handle_about_file_upload(string $fileInputKey, string $prefix = 'about_'): ?string
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

    $filename = uniqid($prefix, true) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $targetPath)) {
        return 'uploads/images/' . $filename;
    }

    throw new Exception('Failed to upload image file.');
}

// Editable Hero & Story Settings Configuration
$aboutFields = [
    'about_heading'        => ['label' => 'Main Heading Title', 'type' => 'text', 'default' => 'About Noor Staffing Group'],
    'about_heading_color'  => ['label' => 'Main Heading Color', 'type' => 'color', 'default' => '#111111'],
    'about_heading_size'   => ['label' => 'Main Heading Font Size', 'type' => 'text', 'default' => '3rem'],
    'about_intro'          => ['label' => 'Introductory Copy', 'type' => 'textarea', 'default' => 'Bridging talent gaps and delivering offshore operational excellence.'],
    'about_intro_color'    => ['label' => 'Intro Text Color', 'type' => 'color', 'default' => '#555555'],
    'about_intro_size'     => ['label' => 'Intro Font Size', 'type' => 'text', 'default' => '1.15rem'],

    'about_story_title'      => ['label' => 'Company Story Section Title', 'type' => 'text', 'default' => 'Our Journey & Strategic Value'],
    'about_story_title_color' => ['label' => 'Company Story Title Color', 'type' => 'color', 'default' => '#111111'],
    'about_story_body'       => ['label' => 'Company Story Body Text', 'type' => 'textarea', 'default' => 'Noor Staffing Group in Bangladesh operates as a vital strategic extension of our US operations.'],
    'about_story_body_color'  => ['label' => 'Company Story Body Color', 'type' => 'color', 'default' => '#444444'],
    'about_page_image'       => ['label' => 'Company Story Featured Image', 'type' => 'image', 'default' => 'assets/dhaka-city.jpg'],

    'management_section_title'       => ['label' => 'Senior Management Section Title', 'type' => 'text', 'default' => 'Message From Senior Management'],
    'management_section_title_color' => ['label' => 'Senior Management Title Color', 'type' => 'color', 'default' => '#111111'],

    'about_mission'       => ['label' => 'Mission Statement Copy', 'type' => 'textarea', 'default' => 'To bridge talent gaps with high-value placement solutions.'],
    'about_mission_color' => ['label' => 'Mission Text Color', 'type' => 'color', 'default' => '#444444'],
    'about_vision'        => ['label' => 'Vision Statement Copy', 'type' => 'textarea', 'default' => 'To be the leading talent acquisition partner built on integrity.'],
    'about_vision_color'  => ['label' => 'Vision Text Color', 'type' => 'color', 'default' => '#444444']
];

// --- POST HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['form_action'] ?? 'save_page_sections';

    try {
        // 1. Save About Page Texts & Featured Story Image
        if ($action === 'save_page_sections') {
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
                'About Us Page',
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

            foreach ($aboutFields as $secKey => $secConf) {
                $val = '';
                if ($secConf['type'] === 'image') {
                    $uploaded = handle_about_file_upload('file_' . $secKey, 'about_story_');
                    $val = $uploaded !== null ? $uploaded : trim((string)($_POST['sec_' . $secKey] ?? ''));
                } else {
                    $val = trim((string)($_POST['sec_' . $secKey] ?? ''));
                }

                $qSection->execute([$pageSlug, $secKey, $secConf['type'], $val]);
            }

            db()->commit();
            $message = 'About page text, colors, and story image saved successfully!';
        }

        // 2. Add / Edit Dynamic Senior Executive Message
        elseif ($action === 'save_exec_message') {
            $execId    = (int)($_POST['exec_id'] ?? 0);
            $fullName  = trim($_POST['full_name'] ?? '');
            $title     = trim($_POST['title'] ?? '');
            $msgText   = trim($_POST['message'] ?? '');
            $biography = trim($_POST['biography'] ?? '');
            $sortOrder = (int)($_POST['sort_order'] ?? 1);
            $status    = $_POST['exec_status'] ?? 'published';

            if (empty($fullName) || empty($title)) {
                throw new Exception('Leader Full Name and Title are required.');
            }

            $photoPath = handle_about_file_upload('exec_photo', 'exec_photo_');
            if ($photoPath === null && isset($_POST['existing_exec_photo'])) {
                $photoPath = $_POST['existing_exec_photo'];
            }

            if ($execId > 0) {
                $stmt = db()->prepare("
                    UPDATE board_members 
                    SET full_name = ?, title = ?, photo_path = ?, message = ?, biography = ?, sort_order = ?, status = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$fullName, $title, $photoPath, $msgText, $biography, $sortOrder, $status, $execId]);
                $message = 'Senior management message updated successfully!';
            } else {
                $stmt = db()->prepare("
                    INSERT INTO board_members (full_name, title, photo_path, message, biography, sort_order, status, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$fullName, $title, $photoPath, $msgText, $biography, $sortOrder, $status]);
                $message = 'New senior executive message added!';
            }
        }

        // 3. Delete Executive Message
        elseif ($action === 'delete_exec_message') {
            $execId = (int)($_POST['exec_id'] ?? 0);
            if ($execId > 0) {
                db()->prepare("DELETE FROM board_members WHERE id = ?")->execute([$execId]);
                $message = 'Executive message removed successfully!';
            }
        }
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

// Fetch Executive Messages List
$seniorManagement = [];
try {
    $qExec = db()->query("SELECT * FROM board_members ORDER BY sort_order ASC, id ASC");
    $seniorManagement = $qExec->fetchAll();
} catch (Throwable $t) {
}

// Selection for Editing Executive Message
$editExec = null;
if (isset($_GET['edit_exec'])) {
    $editId = (int)$_GET['edit_exec'];
    foreach ($seniorManagement as $m) {
        if ((int)$m['id'] === $editId) {
            $editExec = $m;
            break;
        }
    }
}

$pageTitle = 'Manage About Us Page';
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

        .avatar-preview {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #b71c1c;
            vertical-align: middle;
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

        .btn-red {
            background: #b71c1c;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="admin-layout">
        <?= render_admin_sidebar('about') ?>

        <main class="admin-main">
            <h1>Manage About Us Page (`about.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Control company story text, story image uploads, mission/vision statements, and dynamically add/edit multiple senior executive messages (CEO, Directors, Leadership).</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <!-- MODULE 1: DYNAMIC SENIOR MANAGEMENT MESSAGES MANAGER -->
            <div class="card-panel" style="border-top: 4px solid #b71c1c;">
                <h2><?= $editExec ? '✏️ Edit Executive Message' : '➕ Add Senior Management Message (CEO, Director, Executive)' ?></h2>
                <form method="post" action="about.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_exec_message">
                    <input type="hidden" name="exec_id" value="<?= (int)($editExec['id'] ?? 0) ?>">
                    <input type="hidden" name="existing_exec_photo" value="<?= e($editExec['photo_path'] ?? '') ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" value="<?= e($editExec['full_name'] ?? '') ?>" placeholder="e.g. John Noor" required>
                        </div>

                        <div class="form-group">
                            <label>Title / Executive Role *</label>
                            <input type="text" name="title" value="<?= e($editExec['title'] ?? '') ?>" placeholder="e.g. Chief Executive Officer & Managing Director" required>
                        </div>

                        <div class="form-group">
                            <label>Display Order (Position: 1, 2, 3...)</label>
                            <input type="number" name="sort_order" value="<?= (int)($editExec['sort_order'] ?? 1) ?>" min="1">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="exec_status">
                                <option value="published" <?= ($editExec['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= ($editExec['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Executive Photo Upload (Headshot)</label>
                        <input type="file" name="exec_photo" accept="image/*">
                        <?php if (!empty($editExec['photo_path'])): ?>
                            <p style="margin-top:0.5rem;"><img src="<?= e(site_url($editExec['photo_path'])) ?>" class="avatar-preview"> Current Photo</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Featured Quote / Message Highlight</label>
                        <input type="text" name="message" value="<?= e($editExec['message'] ?? '') ?>" placeholder="e.g. Committed to empowering global staffing solutions with trust, transparency, and high performance.">
                    </div>

                    <div class="form-group">
                        <label>Executive Biography & Statement Body</label>
                        <textarea name="biography" rows="3" placeholder="Full biography or detailed executive statement..."><?= e($editExec['biography'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit btn-red"><?= $editExec ? 'Update Message' : 'Save Executive Message' ?></button>
                    <?php if ($editExec): ?>
                        <a href="about.php" style="margin-left: 1rem; color: #666; text-decoration: none; font-weight: 600;">Cancel Edit</a>
                    <?php endif; ?>
                </form>

                <!-- EXECUTIVE MESSAGES LIST TABLE -->
                <h3 style="margin-top: 2rem; font-size: 1.1rem; color: #123B5D;">👥 Current Active Senior Management Messages (<?= count($seniorManagement) ?>)</h3>
                <?php if (empty($seniorManagement)): ?>
                    <p style="color: #888; font-size: 0.9rem; margin-top: 0.5rem;">No executive messages added yet. Add one using the form above.</p>
                <?php else: ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Title</th>
                                <th>Quote</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seniorManagement as $leader): ?>
                                <tr>
                                    <td><strong>#<?= (int)$leader['sort_order'] ?></strong></td>
                                    <td>
                                        <?php if (!empty($leader['photo_path'])): ?>
                                            <img src="<?= e(site_url($leader['photo_path'])) ?>" class="avatar-preview">
                                        <?php else: ?>
                                            <span style="color:#aaa;">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= e($leader['full_name']) ?></strong></td>
                                    <td><?= e($leader['title']) ?></td>
                                    <td><em>"<?= e(substr($leader['message'] ?? '', 0, 50)) ?>..."</em></td>
                                    <td><span style="padding:0.25rem 0.6rem; border-radius:4px; font-size:0.8rem; font-weight:600; background:<?= $leader['status'] === 'published' ? '#e8f5e9; color:#2e7d32;' : '#ffebee; color:#c62828;' ?>"><?= ucfirst($leader['status']) ?></span></td>
                                    <td>
                                        <a href="about.php?edit_exec=<?= (int)$leader['id'] ?>" style="color: #123B5D; font-weight:600; text-decoration:none; margin-right: 0.75rem;">✏️ Edit</a>
                                        <form method="post" action="about.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this executive message?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_exec_message">
                                            <input type="hidden" name="exec_id" value="<?= (int)$leader['id'] ?>">
                                            <button type="submit" style="background:none; border:none; color:#c62828; cursor:pointer; font-weight:600;">🗑️ Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- MODULE 2: PAGE TEXT CONTENT, COLORS & STORY IMAGE -->
            <div class="card-panel">
                <h2>📝 Company Story, Hero Copies & Colors</h2>
                <form method="post" action="about.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_page_sections">

                    <?php foreach ($aboutFields as $key => $conf): ?>
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
                                <input type="text" name="sec_<?= e($key) ?>" id="sec_<?= e($key) ?>" value="<?= e($val) ?>" placeholder="assets/dhaka-city.jpg">
                                <label style="font-size:0.8rem; font-weight:normal; margin-top:0.4rem; color:#666;">Or upload new story image file:</label>
                                <input type="file" name="file_<?= e($key) ?>" accept="image/*">
                            <?php else: ?>
                                <input type="text" name="sec_<?= e($key) ?>" id="sec_<?= e($key) ?>" value="<?= e($val) ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

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

                    <button type="submit" class="btn-submit">Save Page Story & Colors</button>
                </form>
            </div>
        </main>
    </div>

</body>

</html>
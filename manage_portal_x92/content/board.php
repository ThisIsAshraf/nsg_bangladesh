<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('pages.read');

$pageSlug = 'board';
$message  = '';
$error    = '';

// --- HELPER: Upload Leader Photos ---
function handle_leader_photo_upload(string $fileInputKey): ?string
{
    if (!isset($_FILES[$fileInputKey]) || $_FILES[$fileInputKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = $_FILES[$fileInputKey]['tmp_name'];
    $name    = basename($_FILES[$fileInputKey]['name']);
    $ext     = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed, true)) {
        throw new Exception('Invalid image format. Allowed: JPG, PNG, GIF, WEBP');
    }

    $uploadDir = dirname(__DIR__, 2) . '/uploads/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('leader_', true) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $targetPath)) {
        return 'uploads/images/' . $filename;
    }

    throw new Exception('Failed to upload leader photo.');
}

// --- POST HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['form_action'] ?? 'save_hero';

    try {
        // 1. Save Hero Banner & Styling Settings
        if ($action === 'save_hero') {
            db()->beginTransaction();

            $qPage = db()->prepare('
                INSERT INTO pages (title, slug, seo_title, seo_description, status, created_at, updated_at, updated_by) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?) 
                ON DUPLICATE KEY UPDATE seo_title = VALUES(seo_title), seo_description = VALUES(seo_description), status = VALUES(status), updated_at = NOW(), updated_by = VALUES(updated_by)
            ');
            $qPage->execute(['Leadership & Board Page', $pageSlug, trim($_POST['seo_title'] ?? ''), trim($_POST['seo_description'] ?? ''), $_POST['status'] ?? 'published', $admin['id']]);

            $qSection = db()->prepare('
                INSERT INTO page_sections (page_slug, section_key, content_type, content_value) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
            ');

            $boardFields = [
                'board_hero_title'       => $_POST['sec_board_hero_title'] ?? '',
                'board_hero_title_color' => $_POST['sec_board_hero_title_color'] ?? '#111111',
                'board_hero_title_size'  => $_POST['sec_board_hero_title_size'] ?? '3rem',
                'board_intro'            => $_POST['sec_board_intro'] ?? '',
                'board_intro_color'      => $_POST['sec_board_intro_color'] ?? '#555555',
                'board_intro_size'       => $_POST['sec_board_intro_size'] ?? '1.15rem',
            ];

            foreach ($boardFields as $k => $v) {
                $type = str_contains($k, 'color') ? 'color' : (str_contains($k, 'intro') && !str_contains($k, 'size') && !str_contains($k, 'color') ? 'textarea' : 'text');
                $qSection->execute([$pageSlug, $k, $type, trim((string)$v)]);
            }

            db()->commit();
            $message = 'Hero banner and SEO parameters updated successfully!';
        }

        // 2. Add / Edit Leadership Profile
        elseif ($action === 'save_leader') {
            $leaderId  = (int)($_POST['leader_id'] ?? 0);
            $fullName  = trim($_POST['full_name'] ?? '');
            $title     = trim($_POST['title'] ?? '');
            $messageTxt = trim($_POST['message'] ?? '');
            $biography = trim($_POST['biography'] ?? '');
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $status    = $_POST['leader_status'] ?? 'published';

            if (empty($fullName) || empty($title)) {
                throw new Exception('Full Name and Designation Title are required.');
            }

            $photoPath = handle_leader_photo_upload('photo_file');
            if ($photoPath === null && isset($_POST['existing_photo'])) {
                $photoPath = $_POST['existing_photo'];
            }

            if ($leaderId > 0) {
                $stmt = db()->prepare("
                    UPDATE board_members 
                    SET full_name = ?, title = ?, photo_path = ?, message = ?, biography = ?, sort_order = ?, status = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$fullName, $title, $photoPath, $messageTxt, $biography, $sortOrder, $status, $leaderId]);
                $message = 'Leadership profile updated successfully!';
            } else {
                $stmt = db()->prepare("
                    INSERT INTO board_members (full_name, title, photo_path, message, biography, sort_order, status, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$fullName, $title, $photoPath, $messageTxt, $biography, $sortOrder, $status]);
                $message = 'New leader profile added successfully!';
            }
        }

        // 3. Delete Leader Profile
        elseif ($action === 'delete_leader') {
            $leaderId = (int)($_POST['leader_id'] ?? 0);
            if ($leaderId > 0) {
                $stmt = db()->prepare("DELETE FROM board_members WHERE id = ?");
                $stmt->execute([$leaderId]);
                $message = 'Leader profile deleted successfully!';
            }
        }
    } catch (Throwable $t) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        $error = 'Error: ' . $t->getMessage();
    }
}

// --- FETCH DATA ---
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

$allLeaders = [];
try {
    $qL = db()->query("SELECT * FROM board_members ORDER BY sort_order ASC, id ASC");
    $allLeaders = $qL->fetchAll();
} catch (Throwable $t) {
}

$editLeader = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    foreach ($allLeaders as $l) {
        if ((int)$l['id'] === $editId) {
            $editLeader = $l;
            break;
        }
    }
}

$pageTitle = 'Manage Leadership & Board';
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

        /* Flexbox Admin Shell Structure */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Fixed Left Navigation Sidebar */
        .admin-sidebar {
            width: 260px;
            min-width: 260px;
            background: #123B5D;
            color: #fff;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
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
            transition: background 0.2s, color 0.2s;
        }

        .admin-sidebar-nav a:hover,
        .admin-sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-weight: 600;
        }

        /* Flexible Main Panel */
        .admin-main {
            flex: 1;
            padding: 2rem;
            background: #f4f6f8;
            overflow-y: auto;
            max-width: 100%;
        }

        .admin-main h1 {
            font-size: 1.75rem;
            color: #111;
            margin-bottom: 0.25rem;
        }

        .muted {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        /* Panels and Cards */
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

        /* Form Controls */
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

        /* Table Design */
        .leader-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .leader-table th,
        .leader-table td {
            padding: 0.85rem 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }

        .leader-table th {
            background: #fafafa;
            font-weight: 600;
            color: #555;
        }

        .avatar-preview {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #b71c1c;
            vertical-align: middle;
        }

        /* Buttons */
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
        <!-- Render Centralized Navigation -->
        <?= render_admin_sidebar('board') ?>

        <main class="admin-main">
            <h1>Manage Leadership & Board (`board.php`)</h1>
            <p class="muted">Control hero copy, colors, and add/edit executive profiles, headshot photos, and hierarchy order.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <!-- SECTION 1: ADD / EDIT LEADER PROFILE -->
            <div class="card-panel" style="border-top: 4px solid #b71c1c;">
                <h2><?= $editLeader ? '✏️ Edit Leader Profile' : '➕ Add New Leadership Member' ?></h2>
                <form method="post" action="board.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_leader">
                    <input type="hidden" name="leader_id" value="<?= (int)($editLeader['id'] ?? 0) ?>">
                    <input type="hidden" name="existing_photo" value="<?= e($editLeader['photo_path'] ?? '') ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" value="<?= e($editLeader['full_name'] ?? '') ?>" placeholder="e.g. John Doe" required>
                        </div>

                        <div class="form-group">
                            <label>Designation / Title *</label>
                            <input type="text" name="title" value="<?= e($editLeader['title'] ?? '') ?>" placeholder="e.g. Chief Executive Officer" required>
                        </div>

                        <div class="form-group">
                            <label>Sort Order (Hierarchy position: 1, 2, 3...)</label>
                            <input type="number" name="sort_order" value="<?= (int)($editLeader['sort_order'] ?? 1) ?>" min="1">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="leader_status">
                                <option value="published" <?= ($editLeader['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= ($editLeader['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Leader Photo (Upload Headshot Image)</label>
                        <input type="file" name="photo_file" accept="image/*">
                        <?php if (!empty($editLeader['photo_path'])): ?>
                            <p style="margin-top:0.5rem;"><img src="<?= e(site_url($editLeader['photo_path'])) ?>" class="avatar-preview"> Current Photo</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Quote / Leadership Message (Optional)</label>
                        <input type="text" name="message" value="<?= e($editLeader['message'] ?? '') ?>" placeholder="Short inspiring quote or message from leader">
                    </div>

                    <div class="form-group">
                        <label>Full Biography</label>
                        <textarea name="biography" rows="3" placeholder="Overview of experience and role..."><?= e($editLeader['biography'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit btn-red"><?= $editLeader ? 'Update Profile' : 'Save Leader Profile' ?></button>
                    <?php if ($editLeader): ?>
                        <a href="board.php" style="margin-left: 1rem; color: #666; text-decoration: none; font-weight: 600;">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- SECTION 2: CURRENT LEADERSHIP HIERARCHY TABLE -->
            <div class="card-panel">
                <h2>👥 Active Leadership Hierarchy (<?= count($allLeaders) ?> Members)</h2>
                <?php if (empty($allLeaders)): ?>
                    <p class="muted">No leadership profiles found. Add your first leader using the form above.</p>
                <?php else: ?>
                    <table class="leader-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allLeaders as $l): ?>
                                <tr>
                                    <td><strong>#<?= (int)$l['sort_order'] ?></strong></td>
                                    <td>
                                        <?php if (!empty($l['photo_path'])): ?>
                                            <img src="<?= e(site_url($l['photo_path'])) ?>" class="avatar-preview">
                                        <?php else: ?>
                                            <span style="color:#aaa;">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= e($l['full_name']) ?></strong></td>
                                    <td><?= e($l['title']) ?></td>
                                    <td><span style="padding:0.25rem 0.6rem; border-radius:4px; font-size:0.8rem; font-weight:600; background:<?= $l['status'] === 'published' ? '#e8f5e9; color:#2e7d32;' : '#ffebee; color:#c62828;' ?>"><?= ucfirst($l['status']) ?></span></td>
                                    <td>
                                        <a href="board.php?edit=<?= (int)$l['id'] ?>" style="color: #123B5D; font-weight:600; text-decoration:none; margin-right: 0.75rem;">✏️ Edit</a>
                                        <form method="post" action="board.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this leader?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_leader">
                                            <input type="hidden" name="leader_id" value="<?= (int)$l['id'] ?>">
                                            <button type="submit" style="background:none; border:none; color:#c62828; cursor:pointer; font-weight:600;">🗑️ Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- SECTION 3: HERO BANNER TEXT & STYLING -->
            <div class="card-panel">
                <h2>🎨 Page Hero Header Copy & Text Styles</h2>
                <form method="post" action="board.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_hero">

                    <div class="form-group">
                        <label>Leadership Page Title</label>
                        <input type="text" name="sec_board_hero_title" value="<?= e($savedValues['board_hero_title'] ?? 'Executive Leadership & Board') ?>">
                    </div>

                    <div class="form-group">
                        <label>Title Text Color</label>
                        <div class="color-picker-wrap">
                            <input type="color" value="<?= e($savedValues['board_hero_title_color'] ?? '#111111') ?>" onchange="document.getElementById('sec_board_hero_title_color').value = this.value">
                            <input type="text" name="sec_board_hero_title_color" id="sec_board_hero_title_color" value="<?= e($savedValues['board_hero_title_color'] ?? '#111111') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Title Font Size</label>
                        <input type="text" name="sec_board_hero_title_size" value="<?= e($savedValues['board_hero_title_size'] ?? '3rem') ?>">
                    </div>

                    <div class="form-group">
                        <label>Introductory Copy</label>
                        <textarea name="sec_board_intro" rows="3"><?= e($savedValues['board_intro'] ?? 'Guided by experienced leaders dedicated to ethical growth.') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>SEO Browser Title</label>
                        <input type="text" name="seo_title" value="<?= e($pageRecord['seo_title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>SEO Meta Description</label>
                        <textarea name="seo_description" rows="2"><?= e($pageRecord['seo_description'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Save Hero Banner Settings</button>
                </form>
            </div>
        </main>
    </div>

</body>

</html>
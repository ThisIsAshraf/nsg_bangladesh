<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('pages.read');

$pageSlug = 'people';
$message  = '';
$error    = '';

// --- HELPER: Upload Media Assets ---
function handle_people_image_upload(string $fileInputKey, string $prefix = 'people_'): ?string
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

    $filename = uniqid($prefix, true) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $targetPath)) {
        return 'uploads/images/' . $filename;
    }

    throw new Exception('Failed to upload image file.');
}

// --- POST HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['form_action'] ?? 'save_headers';

    try {
        // 1. Save Hero & Section Headers
        if ($action === 'save_headers') {
            db()->beginTransaction();

            $qPage = db()->prepare('
                INSERT INTO pages (title, slug, seo_title, seo_description, status, created_at, updated_at, updated_by) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?) 
                ON DUPLICATE KEY UPDATE seo_title = VALUES(seo_title), seo_description = VALUES(seo_description), status = VALUES(status), updated_at = NOW(), updated_by = VALUES(updated_by)
            ');
            $qPage->execute(['People & Culture Page', $pageSlug, trim($_POST['seo_title'] ?? ''), trim($_POST['seo_description'] ?? ''), $_POST['status'] ?? 'published', $admin['id']]);

            $qSection = db()->prepare('
                INSERT INTO page_sections (page_slug, section_key, content_type, content_value) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
            ');

            $fields = [
                'people_hero_title'       => $_POST['sec_people_hero_title'] ?? '',
                'people_hero_title_color' => $_POST['sec_people_hero_title_color'] ?? '#111111',
                'people_hero_title_size'  => $_POST['sec_people_hero_title_size'] ?? '3rem',
                'people_intro'            => $_POST['sec_people_intro'] ?? '',
                'people_intro_color'      => $_POST['sec_people_intro_color'] ?? '#555555',
                'people_intro_size'       => $_POST['sec_people_intro_size'] ?? '1.15rem',
                'eom_section_title'       => $_POST['sec_eom_section_title'] ?? 'Employee of the Month Spotlight',
                'stories_section_title'   => $_POST['sec_stories_section_title'] ?? 'Employee Stories & Achievements',
                'memories_section_title'  => $_POST['sec_memories_section_title'] ?? 'Team Activities & Moments',
            ];

            foreach ($fields as $k => $v) {
                $type = str_contains($k, 'color') ? 'color' : (str_contains($k, 'intro') && !str_contains($k, 'size') && !str_contains($k, 'color') ? 'textarea' : 'text');
                $qSection->execute([$pageSlug, $k, $type, trim((string)$v)]);
            }

            db()->commit();
            $message = 'Page headers and styling parameters saved successfully!';
        }

        // 2. Add / Edit Employee of the Month
        elseif ($action === 'save_eom') {
            $eomId      = (int)($_POST['eom_id'] ?? 0);
            $name       = trim($_POST['eom_name'] ?? '');
            $role       = trim($_POST['eom_role'] ?? '');
            $monthLabel = trim($_POST['eom_month_label'] ?? '');
            $quote      = trim($_POST['eom_quote'] ?? '');
            $sortOrder  = (int)($_POST['eom_sort_order'] ?? 1);
            $status     = $_POST['eom_status'] ?? 'published';

            if (empty($name) || empty($role) || empty($monthLabel)) {
                throw new Exception('Name, Role, and Month Label are required.');
            }

            $photoPath = handle_people_image_upload('eom_photo', 'eom_');
            if ($photoPath === null && isset($_POST['eom_existing_photo'])) {
                $photoPath = $_POST['eom_existing_photo'];
            }

            if ($eomId > 0) {
                $stmt = db()->prepare("UPDATE employee_of_month SET name = ?, role = ?, month_label = ?, quote = ?, photo_path = ?, sort_order = ?, status = ? WHERE id = ?");
                $stmt->execute([$name, $role, $monthLabel, $quote, $photoPath, $sortOrder, $status, $eomId]);
                $message = 'Employee of the Month spotlight updated!';
            } else {
                $stmt = db()->prepare("INSERT INTO employee_of_month (name, role, month_label, quote, photo_path, sort_order, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $role, $monthLabel, $quote, $photoPath, $sortOrder, $status]);
                $message = 'Employee of the Month spotlight added!';
            }
        }

        // 3. Delete Employee of the Month
        elseif ($action === 'delete_eom') {
            $eomId = (int)($_POST['eom_id'] ?? 0);
            if ($eomId > 0) {
                db()->prepare("DELETE FROM employee_of_month WHERE id = ?")->execute([$eomId]);
                $message = 'Employee of the Month spotlight deleted!';
            }
        }

        // 4. Add / Edit Employee Story
        elseif ($action === 'save_story') {
            $storyId    = (int)($_POST['story_id'] ?? 0);
            $title      = trim($_POST['story_title'] ?? '');
            $authorName = trim($_POST['author_name'] ?? '');
            $authorRole = trim($_POST['author_role'] ?? '');
            $excerpt    = trim($_POST['excerpt'] ?? '');
            $body       = trim($_POST['body'] ?? '');
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $status     = $_POST['story_status'] ?? 'published';

            if (empty($title) || empty($body)) {
                throw new Exception('Story Title and Story Content are required.');
            }

            $coverImg = handle_people_image_upload('cover_image', 'story_');
            if ($coverImg === null && isset($_POST['existing_cover'])) {
                $coverImg = $_POST['existing_cover'];
            }

            if ($storyId > 0) {
                $stmt = db()->prepare("UPDATE employee_stories SET title = ?, author_name = ?, author_role = ?, excerpt = ?, body = ?, cover_image = ?, is_featured = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$title, $authorName, $authorRole, $excerpt, $body, $coverImg, $isFeatured, $status, $storyId]);
                $message = 'Employee Story updated!';
            } else {
                $stmt = db()->prepare("INSERT INTO employee_stories (title, author_name, author_role, excerpt, body, cover_image, is_featured, status, published_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())");
                $stmt->execute([$title, $authorName, $authorRole, $excerpt, $body, $coverImg, $isFeatured, $status]);
                $message = 'Employee Story published!';
            }
        }

        // 5. Delete Employee Story
        elseif ($action === 'delete_story') {
            $storyId = (int)($_POST['story_id'] ?? 0);
            if ($storyId > 0) {
                db()->prepare("DELETE FROM employee_stories WHERE id = ?")->execute([$storyId]);
                $message = 'Employee Story deleted!';
            }
        }

        // 6. Add / Edit Team Activity (Memory)
        elseif ($action === 'save_memory') {
            $memId     = (int)($_POST['mem_id'] ?? 0);
            $title     = trim($_POST['mem_title'] ?? '');
            $caption   = trim($_POST['mem_caption'] ?? '');
            $eventDate = trim($_POST['event_date'] ?? date('Y-m-d'));
            $status    = $_POST['mem_status'] ?? 'published';

            if (empty($title)) {
                throw new Exception('Activity Title is required.');
            }

            $photoPath = handle_people_image_upload('mem_photo', 'mem_');
            if ($photoPath === null && isset($_POST['mem_existing_photo'])) {
                $photoPath = $_POST['mem_existing_photo'];
            }

            if ($memId > 0) {
                $stmt = db()->prepare("UPDATE employee_memories SET title = ?, caption = ?, event_date = ?, photo_path = ?, status = ? WHERE id = ?");
                $stmt->execute([$title, $caption, $eventDate, $photoPath, $status, $memId]);
                $message = 'Team Activity updated!';
            } else {
                $stmt = db()->prepare("INSERT INTO employee_memories (title, caption, event_date, photo_path, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $caption, $eventDate, $photoPath, $status]);
                $message = 'Team Activity photo added to gallery!';
            }
        }

        // 7. Delete Team Activity
        elseif ($action === 'delete_memory') {
            $memId = (int)($_POST['mem_id'] ?? 0);
            if ($memId > 0) {
                db()->prepare("DELETE FROM employee_memories WHERE id = ?")->execute([$memId]);
                $message = 'Activity photo deleted!';
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

$eomList = [];
try {
    $eomList = db()->query("SELECT * FROM employee_of_month ORDER BY sort_order ASC, id DESC")->fetchAll();
} catch (Throwable $t) {
}

$storyList = [];
try {
    $storyList = db()->query("SELECT * FROM employee_stories ORDER BY id DESC")->fetchAll();
} catch (Throwable $t) {
}

$memoryList = [];
try {
    $memoryList = db()->query("SELECT * FROM employee_memories ORDER BY event_date DESC, id DESC")->fetchAll();
} catch (Throwable $t) {
}

// Edit selections
$editEom = null;
if (isset($_GET['edit_eom'])) {
    foreach ($eomList as $item) {
        if ((int)$item['id'] === (int)$_GET['edit_eom']) {
            $editEom = $item;
            break;
        }
    }
}

$editStory = null;
if (isset($_GET['edit_story'])) {
    foreach ($storyList as $item) {
        if ((int)$item['id'] === (int)$_GET['edit_story']) {
            $editStory = $item;
            break;
        }
    }
}

$editMem = null;
if (isset($_GET['edit_mem'])) {
    foreach ($memoryList as $item) {
        if ((int)$item['id'] === (int)$_GET['edit_mem']) {
            $editMem = $item;
            break;
        }
    }
}

$pageTitle = 'Manage People & Culture';
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

        .thumb-preview {
            width: 48px;
            height: 48px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        .btn-submit {
            padding: 0.75rem 1.75rem;
            background: #123B5D;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
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
        <?= render_admin_sidebar('people_pg') ?>

        <main class="admin-main">
            <h1>Manage People & Culture Page (`people.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Control page headers, publish Employee Stories, feature Employee of the Month winners, and post Activity Gallery photos.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <!-- MODULE 1: EMPLOYEE OF THE MONTH SPOTLIGHT -->
            <div class="card-panel" style="border-top: 4px solid #b71c1c;">
                <h2><?= $editEom ? '✏️ Edit Employee of the Month' : '🌟 Add Employee of the Month Spotlight' ?></h2>
                <form method="post" action="people.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_eom">
                    <input type="hidden" name="eom_id" value="<?= (int)($editEom['id'] ?? 0) ?>">
                    <input type="hidden" name="eom_existing_photo" value="<?= e($editEom['photo_path'] ?? '') ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Employee Name *</label>
                            <input type="text" name="eom_name" value="<?= e($editEom['name'] ?? '') ?>" placeholder="e.g. Sarah Ahmed" required>
                        </div>
                        <div class="form-group">
                            <label>Role / Department *</label>
                            <input type="text" name="eom_role" value="<?= e($editEom['role'] ?? '') ?>" placeholder="e.g. Senior Technical Recruiter" required>
                        </div>
                        <div class="form-group">
                            <label>Month Tag *</label>
                            <input type="text" name="eom_month_label" value="<?= e($editEom['month_label'] ?? '') ?>" placeholder="e.g. October 2026" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="eom_status">
                                <option value="published" <?= ($editEom['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= ($editEom['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Employee Headshot Photo Upload</label>
                        <input type="file" name="eom_photo" accept="image/*">
                        <?php if (!empty($editEom['photo_path'])): ?>
                            <p style="margin-top:0.4rem;"><img src="<?= e(site_url($editEom['photo_path'])) ?>" class="thumb-preview"> Current Photo</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Quote / Testimonial</label>
                        <textarea name="eom_quote" rows="2" placeholder="Recognition message or employee quote..."><?= e($editEom['quote'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit btn-red"><?= $editEom ? 'Update Spotlight' : 'Save Employee of Month' ?></button>
                    <?php if ($editEom): ?><a href="people.php" style="margin-left: 1rem; color: #666;">Cancel Edit</a><?php endif; ?>
                </form>

                <?php if (!empty($eomList)): ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eomList as $e): ?>
                                <tr>
                                    <td><strong><?= e($e['month_label']) ?></strong></td>
                                    <td><?php if ($e['photo_path']): ?><img src="<?= e(site_url($e['photo_path'])) ?>" class="thumb-preview"><?php endif; ?></td>
                                    <td><?= e($e['name']) ?></td>
                                    <td><?= e($e['role']) ?></td>
                                    <td><?= ucfirst($e['status']) ?></td>
                                    <td>
                                        <a href="people.php?edit_eom=<?= (int)$e['id'] ?>" style="color:#123B5D; font-weight:600; text-decoration:none; margin-right:0.5rem;">Edit</a>
                                        <form method="post" action="people.php" style="display:inline;" onsubmit="return confirm('Delete this record?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_eom">
                                            <input type="hidden" name="eom_id" value="<?= (int)$e['id'] ?>">
                                            <button type="submit" style="background:none; border:none; color:#c62828; cursor:pointer; font-weight:600;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- MODULE 2: EMPLOYEE STORIES & POSTS -->
            <div class="card-panel" style="border-top: 4px solid #123B5D;">
                <h2><?= $editStory ? '✏️ Edit Employee Story' : '✍️ Publish New Employee Story' ?></h2>
                <form method="post" action="people.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_story">
                    <input type="hidden" name="story_id" value="<?= (int)($editStory['id'] ?? 0) ?>">
                    <input type="hidden" name="existing_cover" value="<?= e($editStory['cover_image'] ?? '') ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Story Title *</label>
                            <input type="text" name="story_title" value="<?= e($editStory['title'] ?? '') ?>" placeholder="e.g. My 5-Year Growth Journey at NSG" required>
                        </div>
                        <div class="form-group">
                            <label>Author Name</label>
                            <input type="text" name="author_name" value="<?= e($editStory['author_name'] ?? '') ?>" placeholder="e.g. Rahul Chowdhury">
                        </div>
                        <div class="form-group">
                            <label>Author Role</label>
                            <input type="text" name="author_role" value="<?= e($editStory['author_role'] ?? '') ?>" placeholder="e.g. Lead Sourcing Specialist">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="story_status">
                                <option value="published" <?= ($editStory['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= ($editStory['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Story Cover Photo</label>
                        <input type="file" name="cover_image" accept="image/*">
                        <?php if (!empty($editStory['cover_image'])): ?>
                            <p style="margin-top:0.4rem;"><img src="<?= e(site_url($editStory['cover_image'])) ?>" class="thumb-preview"> Current Image</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Short Excerpt</label>
                        <input type="text" name="excerpt" value="<?= e($editStory['excerpt'] ?? '') ?>" placeholder="Brief snippet displayed on card grid...">
                    </div>

                    <div class="form-group">
                        <label>Full Story Body *</label>
                        <textarea name="body" rows="4" placeholder="Write full article/story content..." required><?= e($editStory['body'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit"><?= $editStory ? 'Update Story' : 'Publish Story' ?></button>
                    <?php if ($editStory): ?><a href="people.php" style="margin-left: 1rem; color: #666;">Cancel Edit</a><?php endif; ?>
                </form>

                <?php if (!empty($storyList)): ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($storyList as $s): ?>
                                <tr>
                                    <td><?php if ($s['cover_image']): ?><img src="<?= e(site_url($s['cover_image'])) ?>" class="thumb-preview"><?php endif; ?></td>
                                    <td><strong><?= e($s['title']) ?></strong></td>
                                    <td><?= e($s['author_name'] ?? 'N/A') ?></td>
                                    <td><?= ucfirst($s['status']) ?></td>
                                    <td>
                                        <a href="people.php?edit_story=<?= (int)$s['id'] ?>" style="color:#123B5D; font-weight:600; text-decoration:none; margin-right:0.5rem;">Edit</a>
                                        <form method="post" action="people.php" style="display:inline;" onsubmit="return confirm('Delete this story?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_story">
                                            <input type="hidden" name="story_id" value="<?= (int)$s['id'] ?>">
                                            <button type="submit" style="background:none; border:none; color:#c62828; cursor:pointer; font-weight:600;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- MODULE 3: TEAM ACTIVITIES & GALLERY -->
            <div class="card-panel" style="border-top: 4px solid #2e7d32;">
                <h2><?= $editMem ? '✏️ Edit Activity Photo' : '📸 Add Team Activity / Gallery Moment' ?></h2>
                <form method="post" action="people.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_memory">
                    <input type="hidden" name="mem_id" value="<?= (int)($editMem['id'] ?? 0) ?>">
                    <input type="hidden" name="mem_existing_photo" value="<?= e($editMem['photo_path'] ?? '') ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Activity Event Title *</label>
                            <input type="text" name="mem_title" value="<?= e($editMem['title'] ?? '') ?>" placeholder="e.g. Annual Retreat 2026" required>
                        </div>
                        <div class="form-group">
                            <label>Event Date</label>
                            <input type="date" name="event_date" value="<?= e($editMem['event_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="mem_status">
                                <option value="published" <?= ($editMem['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= ($editMem['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Event Photo Upload</label>
                        <input type="file" name="mem_photo" accept="image/*">
                        <?php if (!empty($editMem['photo_path'])): ?>
                            <p style="margin-top:0.4rem;"><img src="<?= e(site_url($editMem['photo_path'])) ?>" class="thumb-preview"> Current Image</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Caption / Description</label>
                        <textarea name="mem_caption" rows="2" placeholder="Brief caption describing the moment..."><?= e($editMem['caption'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit" style="background:#2e7d32;"><?= $editMem ? 'Update Photo' : 'Add Gallery Photo' ?></button>
                    <?php if ($editMem): ?><a href="people.php" style="margin-left: 1rem; color: #666;">Cancel Edit</a><?php endif; ?>
                </form>

                <?php if (!empty($memoryList)): ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($memoryList as $m): ?>
                                <tr>
                                    <td><?php if ($m['photo_path']): ?><img src="<?= e(site_url($m['photo_path'])) ?>" class="thumb-preview"><?php endif; ?></td>
                                    <td><strong><?= e($m['title']) ?></strong></td>
                                    <td><?= e($m['event_date']) ?></td>
                                    <td><?= ucfirst($m['status']) ?></td>
                                    <td>
                                        <a href="people.php?edit_mem=<?= (int)$m['id'] ?>" style="color:#123B5D; font-weight:600; text-decoration:none; margin-right:0.5rem;">Edit</a>
                                        <form method="post" action="people.php" style="display:inline;" onsubmit="return confirm('Delete this image?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_memory">
                                            <input type="hidden" name="mem_id" value="<?= (int)$m['id'] ?>">
                                            <button type="submit" style="background:none; border:none; color:#c62828; cursor:pointer; font-weight:600;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- MODULE 4: PAGE HERO HEADERS & STYLING -->
            <div class="card-panel">
                <h2>🎨 Page Hero Headers & Text Styles</h2>
                <form method="post" action="people.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_headers">

                    <div class="form-group">
                        <label>Page Hero Title</label>
                        <input type="text" name="sec_people_hero_title" value="<?= e($savedValues['people_hero_title'] ?? 'Our People & Culture') ?>">
                    </div>

                    <div class="form-group">
                        <label>Title Text Color</label>
                        <div class="color-picker-wrap">
                            <input type="color" value="<?= e($savedValues['people_hero_title_color'] ?? '#111111') ?>" onchange="document.getElementById('sec_people_hero_title_color').value = this.value">
                            <input type="text" name="sec_people_hero_title_color" id="sec_people_hero_title_color" value="<?= e($savedValues['people_hero_title_color'] ?? '#111111') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Introductory Paragraph</label>
                        <textarea name="sec_people_intro" rows="2"><?= e($savedValues['people_intro'] ?? 'Celebrating individual stories...') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Employee of Month Section Title</label>
                        <input type="text" name="sec_eom_section_title" value="<?= e($savedValues['eom_section_title'] ?? 'Employee of the Month Spotlight') ?>">
                    </div>

                    <div class="form-group">
                        <label>Employee Stories Section Title</label>
                        <input type="text" name="sec_stories_section_title" value="<?= e($savedValues['stories_section_title'] ?? 'Employee Stories & Achievements') ?>">
                    </div>

                    <div class="form-group">
                        <label>Activities Gallery Section Title</label>
                        <input type="text" name="sec_memories_section_title" value="<?= e($savedValues['memories_section_title'] ?? 'Team Activities & Moments') ?>">
                    </div>

                    <div class="form-group">
                        <label>SEO Browser Title</label>
                        <input type="text" name="seo_title" value="<?= e($pageRecord['seo_title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>SEO Meta Description</label>
                        <textarea name="seo_description" rows="2"><?= e($pageRecord['seo_description'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Save Page Headers</button>
                </form>
            </div>
        </main>
    </div>

</body>

</html>
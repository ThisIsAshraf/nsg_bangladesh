<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('jobs.write');

$message = '';
$error   = '';

// --- HELPER: Slug Generator ---
function generate_job_slug(string $title, int $ignoreId = 0): string
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    if (empty($slug)) {
        $slug = 'job-' . time();
    }

    $baseSlug = $slug;
    $counter  = 1;

    while (true) {
        $stmt = db()->prepare("SELECT id FROM jobs WHERE slug = ? AND id != ? LIMIT 1");
        $stmt->execute([$slug, $ignoreId]);
        if (!$stmt->fetch()) {
            break;
        }
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

// --- POST HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['form_action'] ?? 'save_job';

    try {
        // 1. Create or Update Job Circular
        if ($action === 'save_job') {
            $jobId          = (int)($_POST['job_id'] ?? 0);
            $title          = trim($_POST['title'] ?? '');
            $department     = trim($_POST['department'] ?? '');
            $location       = trim($_POST['location'] ?? 'Dhaka, Bangladesh');
            $employmentType = trim($_POST['employment_type'] ?? 'Full-time');
            $summary        = trim($_POST['summary'] ?? '');
            $description    = trim($_POST['description'] ?? '');
            $requirements   = trim($_POST['requirements'] ?? '');
            $closingDate    = !empty($_POST['closing_date']) ? $_POST['closing_date'] : null;
            $status         = $_POST['status'] ?? 'published';
            $isFeatured     = isset($_POST['is_featured']) ? 1 : 0;

            if (empty($title) || empty($description)) {
                throw new Exception('Job Title and Job Description are required.');
            }

            $slug = generate_job_slug($title, $jobId);

            if ($jobId > 0) {
                $stmt = db()->prepare("
                    UPDATE jobs 
                    SET title = ?, slug = ?, department = ?, location = ?, employment_type = ?, summary = ?, description = ?, requirements = ?, closing_date = ?, status = ?, is_featured = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([
                    $title,
                    $slug,
                    $department,
                    $location,
                    $employmentType,
                    $summary,
                    $description,
                    $requirements,
                    $closingDate,
                    $status,
                    $isFeatured,
                    $jobId
                ]);
                $message = 'Job circular updated successfully!';
            } else {
                $stmt = db()->prepare("
                    INSERT INTO jobs (title, slug, department, location, employment_type, summary, description, requirements, closing_date, status, is_featured, published_at, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())
                ");
                $stmt->execute([
                    $title,
                    $slug,
                    $department,
                    $location,
                    $employmentType,
                    $summary,
                    $description,
                    $requirements,
                    $closingDate,
                    $status,
                    $isFeatured
                ]);
                $message = 'New job circular published successfully!';
            }
        }

        // 2. Delete Job Circular
        elseif ($action === 'delete_job') {
            $jobId = (int)($_POST['job_id'] ?? 0);
            if ($jobId > 0) {
                db()->prepare("DELETE FROM jobs WHERE id = ?")->execute([$jobId]);
                $message = 'Job circular deleted successfully!';
            }
        }
    } catch (Throwable $t) {
        $error = 'Error: ' . $t->getMessage();
    }
}

// --- FETCH DATA ---
$searchTerm = trim($_GET['q'] ?? '');
$allJobs = [];
try {
    $sql = "SELECT * FROM jobs";
    $params = [];
    if (!empty($searchTerm)) {
        $sql .= " WHERE title LIKE ? OR department LIKE ? OR location LIKE ?";
        $term = '%' . $searchTerm . '%';
        $params = [$term, $term, $term];
    }
    $sql .= " ORDER BY is_featured DESC, id DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $allJobs = $stmt->fetchAll();
} catch (Throwable $t) {
}

// Edit selection
$editJob = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    foreach ($allJobs as $j) {
        if ((int)$j['id'] === $editId) {
            $editJob = $j;
            break;
        }
    }
}

$pageTitle = 'Manage Job Openings';
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
        <?= render_admin_sidebar('jobs') ?>

        <main class="admin-main">
            <h1>Manage Job Circulars (`jobs.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Create, edit, feature, and manage open vacancy listings displayed across `careers.php` and `job.php`.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <!-- MODULE 1: CREATE / EDIT JOB FORM -->
            <div class="card-panel" style="border-top: 4px solid #b71c1c;">
                <h2><?= $editJob ? '✏️ Edit Job Circular' : '💼 Post New Job Circular' ?></h2>
                <form method="post" action="jobs.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_action" value="save_job">
                    <input type="hidden" name="job_id" value="<?= (int)($editJob['id'] ?? 0) ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Job Title *</label>
                            <input type="text" name="title" value="<?= e($editJob['title'] ?? '') ?>" placeholder="e.g. Senior Technical Recruiter" required>
                        </div>

                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" name="department" value="<?= e($editJob['department'] ?? '') ?>" placeholder="e.g. Talent Acquisition">
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" value="<?= e($editJob['location'] ?? 'Dhaka, Bangladesh') ?>">
                        </div>

                        <div class="form-group">
                            <label>Employment Type</label>
                            <select name="employment_type">
                                <option value="Full-time" <?= ($editJob['employment_type'] ?? 'Full-time') === 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                <option value="Part-time" <?= ($editJob['employment_type'] ?? '') === 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                <option value="Contract" <?= ($editJob['employment_type'] ?? '') === 'Contract' ? 'selected' : '' ?>>Contract</option>
                                <option value="Remote" <?= ($editJob['employment_type'] ?? '') === 'Remote' ? 'selected' : '' ?>>Remote</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Application Deadline</label>
                            <input type="date" name="closing_date" value="<?= e($editJob['closing_date'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Publication Status</label>
                            <select name="status">
                                <option value="published" <?= ($editJob['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= ($editJob['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>

                        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem;">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1" <?= !empty($editJob['is_featured']) ? 'checked' : '' ?> style="width: auto;">
                            <label for="is_featured" style="margin-bottom: 0;">Feature this job on Home Page</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Short Role Summary</label>
                        <input type="text" name="summary" value="<?= e($editJob['summary'] ?? '') ?>" placeholder="Brief 1-2 sentence overview for job cards...">
                    </div>

                    <div class="form-group">
                        <label>Full Job Description & Responsibilities *</label>
                        <textarea name="description" rows="5" placeholder="Detailed job responsibilities and duties..." required><?= e($editJob['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Requirements & Qualifications</label>
                        <textarea name="requirements" rows="4" placeholder="Skills, degrees, or experience required..."><?= e($editJob['requirements'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit btn-red"><?= $editJob ? 'Update Circular' : 'Publish Job Opening' ?></button>
                    <?php if ($editJob): ?>
                        <a href="jobs.php" style="margin-left: 1rem; color: #666; text-decoration: none; font-weight: 600;">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- MODULE 2: LIST OF ALL JOB OPENINGS -->
            <div class="card-panel">
                <h2>📋 Active Vacancies (<?= count($allJobs) ?>)</h2>

                <form method="get" action="jobs.php" style="margin-bottom: 1rem; display: flex; gap: 0.5rem;">
                    <input type="text" name="q" value="<?= e($searchTerm) ?>" placeholder="Search jobs by title or department..." style="flex: 1; padding: 0.6rem; border: 1px solid #ccc; border-radius: 4px;">
                    <button type="submit" class="btn-submit" style="padding: 0.6rem 1.25rem;">Search</button>
                </form>

                <?php if (empty($allJobs)): ?>
                    <p style="color: #888; font-size: 0.9rem;">No job circulars found. Create your first listing using the form above.</p>
                <?php else: ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Department</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Featured</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allJobs as $job): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($job['title']) ?></strong>
                                        <br><small style="color: #888;">Slug: <?= e($job['slug']) ?></small>
                                    </td>
                                    <td><?= e($job['department'] ?: 'General') ?></td>
                                    <td><?= e($job['location']) ?></td>
                                    <td><?= e($job['employment_type']) ?></td>
                                    <td><?= !empty($job['is_featured']) ? '⭐ Yes' : 'No' ?></td>
                                    <td>
                                        <span style="padding: 0.25rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; background: <?= $job['status'] === 'published' ? '#e8f5e9; color:#2e7d32;' : '#ffebee; color:#c62828;' ?>">
                                            <?= ucfirst($job['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="jobs.php?edit=<?= (int)$job['id'] ?>" style="color: #123B5D; font-weight: 600; text-decoration: none; margin-right: 0.75rem;">✏️ Edit</a>
                                        <a href="<?= e(site_url('job.php?slug=' . rawurlencode($job['slug']))) ?>" target="_blank" style="color: #2e7d32; font-weight: 600; text-decoration: none; margin-right: 0.75rem;">👁️ View</a>
                                        <form method="post" action="jobs.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this job circular?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_job">
                                            <input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
                                            <button type="submit" style="background: none; border: none; color: #c62828; cursor: pointer; font-weight: 600;">🗑️ Delete</button>
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
<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('applications.read');

$message = '';
$error   = '';

// --- POST HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['form_action'] ?? '';

    try {
        // 1. Update Application Status & Notes
        if ($action === 'update_status') {
            $appId  = (int)($_POST['app_id'] ?? 0);
            $status = $_POST['status'] ?? 'pending';
            $notes  = trim($_POST['admin_notes'] ?? '');

            if ($appId > 0) {
                $stmt = db()->prepare("
                    UPDATE job_applications 
                    SET status = ?, admin_notes = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$status, $notes, $appId]);
                $message = 'Application status updated successfully!';
            }
        }

        // 2. Delete Application Record
        elseif ($action === 'delete_app') {
            $appId = (int)($_POST['app_id'] ?? 0);
            if ($appId > 0) {
                // Fetch file path to clean up storage
                $q = db()->prepare("SELECT resume_path FROM job_applications WHERE id = ?");
                $q->execute([$appId]);
                $row = $q->fetch();

                if ($row && !empty($row['resume_path'])) {
                    $fullPath = dirname(__DIR__, 2) . '/' . $row['resume_path'];
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }

                db()->prepare("DELETE FROM job_applications WHERE id = ?")->execute([$appId]);
                $message = 'Application record deleted successfully!';
            }
        }
    } catch (Throwable $t) {
        $error = 'Error: ' . $t->getMessage();
    }
}

// --- FETCH DATA ---
$statusFilter = trim($_GET['status'] ?? '');
$searchTerm   = trim($_GET['q'] ?? '');

$applications = [];
try {
    $sql = "
        SELECT a.*, j.title AS job_title 
        FROM job_applications a 
        LEFT JOIN jobs j ON a.job_id = j.id
    ";
    $conditions = [];
    $params     = [];

    if (!empty($statusFilter)) {
        $conditions[] = "a.status = ?";
        $params[] = $statusFilter;
    }

    if (!empty($searchTerm)) {
        $conditions[] = "(a.applicant_name LIKE ? OR a.applicant_email LIKE ? OR j.title LIKE ?)";
        $term = '%' . $searchTerm . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY a.id DESC";

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $applications = $stmt->fetchAll();
} catch (Throwable $t) {
    $applications = [];
}

// Detailed View Selection
$viewApp = null;
if (isset($_GET['view'])) {
    $viewId = (int)$_GET['view'];
    foreach ($applications as $app) {
        if ((int)$app['id'] === $viewId) {
            $viewApp = $app;
            break;
        }
    }
}

$pageTitle = 'Manage Candidate Applications';
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

        .badge-status {
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: #fff3e0;
            color: #e65100;
        }

        .status-reviewed {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-shortlisted {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-rejected {
            background: #ffebee;
            color: #c62828;
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
        <?= render_admin_sidebar('apps') ?>

        <main class="admin-main">
            <h1>Candidate Applications Portal (`applications.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Review submitted candidate resumes, update recruitment status, and manage applicant details.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <!-- DETAIL APPLICATION VIEW MODAL / PANEL -->
            <?php if ($viewApp): ?>
                <div class="card-panel" style="border-top: 4px solid #123B5D;">
                    <h2>📄 Applicant Detail: <?= e($viewApp['applicant_name']) ?></h2>
                    <div class="form-grid" style="margin-bottom: 1.5rem;">
                        <div><strong>Applied Position:</strong> <?= e($viewApp['job_title'] ?: 'General Application') ?></div>
                        <div><strong>Email:</strong> <a href="mailto:<?= e($viewApp['applicant_email']) ?>"><?= e($viewApp['applicant_email']) ?></a></div>
                        <div><strong>Phone:</strong> <?= e($viewApp['applicant_phone'] ?: 'N/A') ?></div>
                        <div><strong>Submitted Date:</strong> <?= e(date('M d, Y H:i', strtotime($viewApp['created_at']))) ?></div>
                    </div>

                    <div class="form-group">
                        <label>Cover Note / Summary</label>
                        <div style="background: #fafafa; padding: 1rem; border-radius: 4px; border: 1px solid #eee; font-size: 0.95rem;"><?= nl2br(e($viewApp['cover_letter'] ?: 'No cover note provided.')) ?></div>
                    </div>

                    <div class="form-group">
                        <label>Candidate Resume / CV</label>
                        <?php if (!empty($viewApp['resume_path'])): ?>
                            <a href="<?= e(site_url($viewApp['resume_path'])) ?>" target="_blank" style="display: inline-block; padding: 0.6rem 1.25rem; background: #2e7d32; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 0.875rem;">📥 Download CV / Resume</a>
                        <?php else: ?>
                            <span style="color: #888;">No CV file uploaded.</span>
                        <?php endif; ?>
                    </div>

                    <form method="post" action="applications.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="form_action" value="update_status">
                        <input type="hidden" name="app_id" value="<?= (int)$viewApp['id'] ?>">

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Recruitment Status</label>
                                <select name="status">
                                    <option value="pending" <?= $viewApp['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="reviewed" <?= $viewApp['status'] === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                    <option value="shortlisted" <?= $viewApp['status'] === 'shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                                    <option value="rejected" <?= $viewApp['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>HR Internal Notes</label>
                            <textarea name="admin_notes" rows="3" placeholder="Add internal recruiter feedback or interview notes..."><?= e($viewApp['admin_notes'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Update Application Status</button>
                        <a href="applications.php" style="margin-left: 1rem; color: #666; text-decoration: none; font-weight: 600;">Close Detail</a>
                    </form>
                </div>
            <?php endif; ?>

            <!-- MODULE 2: APPLICATIONS LIST -->
            <div class="card-panel">
                <h2>📥 Submitted Applications (<?= count($applications) ?>)</h2>

                <form method="get" action="applications.php" style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <input type="text" name="q" value="<?= e($searchTerm) ?>" placeholder="Search by name, email, or job title..." style="flex: 2; min-width: 200px; padding: 0.6rem; border: 1px solid #ccc; border-radius: 4px;">
                    <select name="status" style="flex: 1; min-width: 150px; padding: 0.6rem; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="reviewed" <?= $statusFilter === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                        <option value="shortlisted" <?= $statusFilter === 'shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                        <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                    <button type="submit" class="btn-submit" style="padding: 0.6rem 1.25rem;">Filter</button>
                    <?php if ($searchTerm !== '' || $statusFilter !== ''): ?>
                        <a href="applications.php" style="align-self: center; color: #666; font-size: 0.85rem; text-decoration: underline;">Reset Filters</a>
                    <?php endif; ?>
                </form>

                <?php if (empty($applications)): ?>
                    <p style="color: #888; font-size: 0.9rem;">No candidate applications found.</p>
                <?php else: ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Applicant</th>
                                <th>Applied Position</th>
                                <th>Resume</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><small style="color: #666;"><?= e(date('M d, Y', strtotime($app['created_at']))) ?></small></td>
                                    <td>
                                        <strong><?= e($app['applicant_name']) ?></strong>
                                        <br><small><a href="mailto:<?= e($app['applicant_email']) ?>" style="color: #123B5D;"><?= e($app['applicant_email']) ?></a></small>
                                    </td>
                                    <td><?= e($app['job_title'] ?: 'General Application') ?></td>
                                    <td>
                                        <?php if (!empty($app['resume_path'])): ?>
                                            <a href="<?= e(site_url($app['resume_path'])) ?>" target="_blank" style="color: #2e7d32; font-weight: 600; text-decoration: none;">📥 Download</a>
                                        <?php else: ?>
                                            <span style="color: #aaa;">No File</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-status status-<?= e($app['status'] ?: 'pending') ?>">
                                            <?= ucfirst($app['status'] ?: 'pending') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="applications.php?view=<?= (int)$app['id'] ?>" style="color: #123B5D; font-weight: 600; text-decoration: none; margin-right: 0.75rem;">👁️ Review</a>
                                        <form method="post" action="applications.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this candidate application?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_app">
                                            <input type="hidden" name="app_id" value="<?= (int)$app['id'] ?>">
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
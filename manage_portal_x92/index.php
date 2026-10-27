<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

// Fetch quick dashboard metrics
$stats = [
    'visits_today' => 0,
    'visits_total' => 0,
    'pages'        => 0,
    'jobs'         => 0,
    'applications' => 0,
    'messages'     => 0,
    'eom'          => 0,
    'memories'     => 0,
    'board'        => 0
];

try {
    $stats['pages']        = (int)db()->query("SELECT COUNT(*) FROM pages")->fetchColumn();
    $stats['jobs']         = (int)db()->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
    $stats['applications'] = (int)db()->query("SELECT COUNT(*) FROM job_applications")->fetchColumn();
    $stats['messages']     = (int)db()->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    $stats['board']        = (int)db()->query("SELECT COUNT(*) FROM board_members")->fetchColumn();
} catch (Throwable $t) {
}

// Fetch recent activity logs
$activityLogs = [];
try {
    $activityLogs = db()->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 10")->fetchAll();
} catch (Throwable $t) {
}

$pageTitle = 'Dashboard Overview';
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #123B5D;
        }

        .stat-card.accent {
            border-left-color: #b71c1c;
        }

        .stat-card label {
            display: block;
            font-size: 0.8rem;
            color: #666;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111;
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
    </style>
</head>

<body>

    <div class="admin-layout">
        <?= render_admin_sidebar('dashboard') ?>

        <main class="admin-main">
            <h1>Dashboard Overview</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Welcome back, <strong><?= e($admin['username'] ?? 'Admin') ?></strong>.</p>

            <!-- Stats Overview Grid -->
            <div class="stats-grid">
                <div class="stat-card accent">
                    <label>Total Pages</label>
                    <div class="value"><?= $stats['pages'] ?></div>
                </div>
                <div class="stat-card">
                    <label>Job Openings</label>
                    <div class="value"><?= $stats['jobs'] ?></div>
                </div>
                <div class="stat-card">
                    <label>Applications</label>
                    <div class="value"><?= $stats['applications'] ?></div>
                </div>
                <div class="stat-card">
                    <label>Contact Messages</label>
                    <div class="value"><?= $stats['messages'] ?></div>
                </div>
                <div class="stat-card">
                    <label>Board Members</label>
                    <div class="value"><?= $stats['board'] ?></div>
                </div>
            </div>

            <!-- Recent System Activity Logs -->
            <div class="card-panel">
                <h2>Recent Activity Logs</h2>
                <?php if (empty($activityLogs)): ?>
                    <p style="color: #888; font-size: 0.9rem;">No recent activity recorded.</p>
                <?php else: ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Action</th>
                                <th>Entity / Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activityLogs as $log): ?>
                                <tr>
                                    <td><small style="color: #666;"><?= e(date('M d, Y H:i', strtotime($log['created_at']))) ?></small></td>
                                    <td><strong><?= e($log['action'] ?? 'system_action') ?></strong></td>
                                    <td><?= e($log['entity_type'] ?? '') ?> #<?= e((string)($log['entity_id'] ?? '')) ?></td>
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
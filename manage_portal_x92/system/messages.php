<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

require_admin('pages.read');

$message = '';
$error   = '';

// --- POST HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['form_action'] ?? '';

    try {
        // 1. Mark Status (Read / Unread / Replied)
        if ($action === 'update_status') {
            $msgId  = (int)($_POST['msg_id'] ?? 0);
            $status = $_POST['status'] ?? 'read';

            if ($msgId > 0) {
                $stmt = db()->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
                $stmt->execute([$status, $msgId]);
                $message = 'Message status updated successfully!';
            }
        }

        // 2. Delete Message Record
        elseif ($action === 'delete_msg') {
            $msgId = (int)($_POST['msg_id'] ?? 0);
            if ($msgId > 0) {
                db()->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$msgId]);
                $message = 'Contact message deleted successfully!';
            }
        }
    } catch (Throwable $t) {
        $error = 'Error: ' . $t->getMessage();
    }
}

// --- FETCH DATA ---
$statusFilter = trim($_GET['status'] ?? '');
$searchTerm   = trim($_GET['q'] ?? '');

$messagesList = [];
try {
    $sql = "SELECT * FROM contact_messages";
    $conditions = [];
    $params     = [];

    if (!empty($statusFilter)) {
        $conditions[] = "status = ?";
        $params[] = $statusFilter;
    }

    if (!empty($searchTerm)) {
        $conditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
        $term = '%' . $searchTerm . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY id DESC";

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $messagesList = $stmt->fetchAll();
} catch (Throwable $t) {
    $messagesList = [];
}

// View Message Selection
$viewMsg = null;
if (isset($_GET['view'])) {
    $viewId = (int)$_GET['view'];
    foreach ($messagesList as $m) {
        if ((int)$m['id'] === $viewId) {
            $viewMsg = $m;
            // Automatically mark as read when viewed
            if (($viewMsg['status'] ?? '') === 'unread') {
                try {
                    db()->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$viewId]);
                    $viewMsg['status'] = 'read';
                } catch (Throwable $t) {
                }
            }
            break;
        }
    }
}

$pageTitle = 'Manage Contact Messages';
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

        .status-unread {
            background: #ffebee;
            color: #c62828;
        }

        .status-read {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-replied {
            background: #e8f5e9;
            color: #2e7d32;
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
        <?= render_admin_sidebar('messages') ?>

        <main class="admin-main">
            <h1>Contact Messages Portal (`messages.php`)</h1>
            <p style="color: #666; margin-bottom: 1.5rem;">Review, manage, and respond to general inquiries submitted via the Contact page.</p>

            <?php if ($message): ?><p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px; margin-bottom: 1.5rem;"><?= e($error) ?></p><?php endif; ?>

            <!-- VIEW MESSAGE MODAL / DETAIL CARD -->
            <?php if ($viewMsg): ?>
                <div class="card-panel" style="border-top: 4px solid #123B5D;">
                    <h2>✉️ Message Detail: <?= e($viewMsg['subject'] ?: 'General Inquiry') ?></h2>
                    <div class="form-grid" style="margin-bottom: 1.5rem;">
                        <div><strong>From:</strong> <?= e($viewMsg['name']) ?></div>
                        <div><strong>Email:</strong> <a href="mailto:<?= e($viewMsg['email']) ?>" style="color:#123B5D; font-weight:600;"><?= e($viewMsg['email']) ?></a></div>
                        <div><strong>Submitted Date:</strong> <?= e(date('M d, Y H:i', strtotime($viewMsg['created_at']))) ?></div>
                    </div>

                    <div class="form-group">
                        <label>Full Message Body</label>
                        <div style="background: #fafafa; padding: 1.25rem; border-radius: 4px; border: 1px solid #eee; font-size: 0.95rem; line-height: 1.6; color: #222;"><?= nl2br(e($viewMsg['message'])) ?></div>
                    </div>

                    <form method="post" action="messages.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="form_action" value="update_status">
                        <input type="hidden" name="msg_id" value="<?= (int)$viewMsg['id'] ?>">

                        <div class="form-grid" style="align-items: flex-end;">
                            <div class="form-group">
                                <label>Mark Status</label>
                                <select name="status">
                                    <option value="read" <?= ($viewMsg['status'] ?? '') === 'read' ? 'selected' : '' ?>>Read</option>
                                    <option value="unread" <?= ($viewMsg['status'] ?? '') === 'unread' ? 'selected' : '' ?>>Unread</option>
                                    <option value="replied" <?= ($viewMsg['status'] ?? '') === 'replied' ? 'selected' : '' ?>>Replied</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-submit">Update Status</button>
                                <a href="mailto:<?= e($viewMsg['email']) ?>?subject=Re: <?= rawurlencode($viewMsg['subject'] ?? 'Inquiry') ?>" style="display: inline-block; margin-left: 0.5rem; padding: 0.85rem 1.5rem; background: #2e7d32; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 0.95rem;">📧 Reply via Email</a>
                                <a href="messages.php" style="margin-left: 1rem; color: #666; text-decoration: none; font-weight: 600;">Close View</a>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- MODULE 2: INBOX MESSAGES LIST -->
            <div class="card-panel">
                <h2>📥 Contact Inbox (<?= count($messagesList) ?>)</h2>

                <form method="get" action="messages.php" style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <input type="text" name="q" value="<?= e($searchTerm) ?>" placeholder="Search by name, email, or message body..." style="flex: 2; min-width: 200px; padding: 0.6rem; border: 1px solid #ccc; border-radius: 4px;">
                    <select name="status" style="flex: 1; min-width: 150px; padding: 0.6rem; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">All Statuses</option>
                        <option value="unread" <?= $statusFilter === 'unread' ? 'selected' : '' ?>>Unread</option>
                        <option value="read" <?= $statusFilter === 'read' ? 'selected' : '' ?>>Read</option>
                        <option value="replied" <?= $statusFilter === 'replied' ? 'selected' : '' ?>>Replied</option>
                    </select>
                    <button type="submit" class="btn-submit" style="padding: 0.6rem 1.25rem;">Filter</button>
                    <?php if ($searchTerm !== '' || $statusFilter !== ''): ?>
                        <a href="messages.php" style="align-self: center; color: #666; font-size: 0.85rem; text-decoration: underline;">Reset Filters</a>
                    <?php endif; ?>
                </form>

                <?php if (empty($messagesList)): ?>
                    <p style="color: #888; font-size: 0.9rem;">No contact messages found.</p>
                <?php else: ?>
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Excerpt</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messagesList as $m): ?>
                                <tr style="<?= ($m['status'] ?? '') === 'unread' ? 'font-weight: 600; background: #fffde7;' : '' ?>">
                                    <td><small style="color: #666;"><?= e(date('M d, Y H:i', strtotime($m['created_at']))) ?></small></td>
                                    <td>
                                        <?= e($m['name']) ?>
                                        <br><small><a href="mailto:<?= e($m['email']) ?>" style="color: #123B5D; font-weight: normal;"><?= e($m['email']) ?></a></small>
                                    </td>
                                    <td><?= e($m['subject'] ?: 'General Inquiry') ?></td>
                                    <td><?= e(substr($m['message'], 0, 50)) ?>...</td>
                                    <td>
                                        <span class="badge-status status-<?= e($m['status'] ?: 'unread') ?>">
                                            <?= ucfirst($m['status'] ?: 'unread') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="messages.php?view=<?= (int)$m['id'] ?>" style="color: #123B5D; font-weight: 600; text-decoration: none; margin-right: 0.75rem;">👁️ Read</a>
                                        <form method="post" action="messages.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="form_action" value="delete_msg">
                                            <input type="hidden" name="msg_id" value="<?= (int)$m['id'] ?>">
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
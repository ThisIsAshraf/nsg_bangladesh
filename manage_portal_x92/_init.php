<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

// Enforce admin authentication
require_admin();

// Store current logged-in admin array for usage across admin view templates
$admin = admin_user();

/**
 * Render standard admin sidebar navigation menu cleanly
 */
function render_admin_sidebar(string $activeKey = ''): string
{
    $siteName = e(setting('site_name', 'Noor Staffing'));

    // Option B: Streamlined single-manager routes inside content/ & feature folders
    $items = [
        'dashboard'  => ['label' => '📊 Dashboard', 'url' => site_url('admin/index.php')],
        'about'      => ['label' => '📄 About Us Page', 'url' => site_url('admin/content/about.php')],
        'board'      => ['label' => '📄 Leadership & Board', 'url' => site_url('admin/content/board.php')],
        'people_pg'  => ['label' => '📄 People & Culture Page', 'url' => site_url('admin/content/people.php')],
        'contact_pg' => ['label' => '📄 Contact Page', 'url' => site_url('admin/content/contact.php')],
        'careers_pg' => ['label' => '📄 Careers Page', 'url' => site_url('admin/content/careers.php')],
        'apply_pg'   => ['label' => '📄 Apply Page', 'url' => site_url('admin/content/apply.php')],
        'jobs'       => ['label' => '💼 Job Listings', 'url' => site_url('admin/careers/jobs.php'), 'perm' => 'jobs.write'],
        'apps'       => ['label' => '📥 Applications', 'url' => site_url('admin/careers/applications.php'), 'perm' => 'applications.read'],
        'messages'   => ['label' => '✉️ Contact Messages', 'url' => site_url('admin/system/messages.php')],
        'settings'   => ['label' => '⚙️ Settings & Theme', 'url' => site_url('admin/system/settings.php')],
    ];

    $html = '<aside class="admin-sidebar">';
    $html .= '<h2>' . $siteName . '</h2>';
    $html .= '<nav class="admin-sidebar-nav">';

    foreach ($items as $key => $item) {
        if (isset($item['perm']) && !(has_permission('*') || has_permission($item['perm']))) {
            continue;
        }
        $activeClass = ($activeKey === $key) ? ' class="active"' : '';
        $html .= '<a href="' . e($item['url']) . '"' . $activeClass . '>' . $item['label'] . '</a>';
    }

    $html .= '<a href="' . e(site_url('admin/auth/sign-out.php')) . '" style="margin-top: 2rem; color: #ff8a80;">🚪 Sign Out</a>';
    $html .= '</nav>';
    $html .= '</aside>';

    return $html;
}

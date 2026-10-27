<?php

declare(strict_types=1);

require dirname(__DIR__) . '/_init.php';

// Enforce permission check for dynamic page management
require_admin('pages.read');

$selectedSlug = $_GET['page'] ?? 'index';
$action       = $_GET['action'] ?? 'edit';

$message = '';
$error   = '';

// --- HELPER: Handle Image Uploads for Page Sections ---
function handle_section_image_upload(string $fileInputKey): ?string
{
    if (!isset($_FILES[$fileInputKey]) || $_FILES[$fileInputKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = $_FILES[$fileInputKey]['tmp_name'];
    $name    = basename($_FILES[$fileInputKey]['name']);
    $ext     = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (!in_array($ext, $allowed, true)) {
        throw new Exception('Invalid image file format. Allowed: JPG, PNG, GIF, WEBP, SVG');
    }

    $uploadDir = dirname(__DIR__, 2) . '/uploads/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('page_img_', true) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $targetPath)) {
        return 'uploads/images/' . $filename;
    }

    throw new Exception('Failed to upload image.');
}

// --- Comprehensive Editable Sections Configuration across ALL Pages ---
$editablePages = [
    'index' => [
        'title' => 'Home Page',
        'sections' => [
            // 1. Hero Section
            'hero_title' => ['label' => 'Hero Main Tagline', 'type' => 'text', 'default' => 'Operational excellence with global impact'],
            'hero_title_color' => ['label' => 'Hero Title Text Color', 'type' => 'color', 'default' => '#ffffff'],
            'hero_title_size' => ['label' => 'Hero Title Font Size (e.g. 3.5rem)', 'type' => 'text', 'default' => '3.5rem'],
            'hero_image' => ['label' => 'Hero Background Image', 'type' => 'image', 'default' => 'assets/hero-city.jpg'],

            // 2. About Section
            'home_about_heading' => ['label' => 'About Section Heading', 'type' => 'text', 'default' => 'Noor Staffing Group in Bangladesh'],
            'home_about_heading_color' => ['label' => 'About Heading Text Color', 'type' => 'color', 'default' => '#111111'],
            'home_about_heading_size' => ['label' => 'About Heading Font Size', 'type' => 'text', 'default' => '2.5rem'],
            'home_about_body' => ['label' => 'About Section Body Copy', 'type' => 'textarea', 'default' => 'Noor Staffing Group, part of the Noor, Inc. group of companies, supports its nationwide US staffing and workforce solutions through a dedicated offshore support team based in Bangladesh.'],
            'home_about_body_color' => ['label' => 'About Body Text Color', 'type' => 'color', 'default' => '#444444'],
            'about_section_image' => ['label' => 'About Section Feature Image', 'type' => 'image', 'default' => 'assets/dhaka-city.jpg'],

            // 3. CTA Banner Section
            'cta_heading' => ['label' => 'CTA Banner Text', 'type' => 'text', 'default' => 'Take the first step toward your dreams.'],
            'cta_heading_color' => ['label' => 'CTA Banner Text Color', 'type' => 'color', 'default' => '#111111'],
            'cta_button_label' => ['label' => 'CTA Button Text', 'type' => 'text', 'default' => 'SUBMIT YOUR RESUME'],

            // 4. At A Glance Stats (5 Items)
            'stat_1' => ['label' => 'Stat Item 1', 'type' => 'text', 'default' => '20+ Years in operation'],
            'stat_2' => ['label' => 'Stat Item 2', 'type' => 'text', 'default' => '14 US offices supporting clients in 46 states'],
            'stat_3' => ['label' => 'Stat Item 3', 'type' => 'text', 'default' => '250+ corporate staff in the U.S. and 100+ team members in Bangladesh'],
            'stat_4' => ['label' => 'Stat Item 4', 'type' => 'text', 'default' => '22 specialized brands under the Noor, Inc. portfolio'],
            'stat_5' => ['label' => 'Stat Item 5', 'type' => 'text', 'default' => '6,000+ contingent workers actively employed nationwide'],

            // 5. Vision Quote Section
            'vision_quote' => ['label' => 'Vision Quote Text', 'type' => 'text', 'default' => 'Driven by talent and built on trust.'],
            'vision_quote_color' => ['label' => 'Vision Quote Color', 'type' => 'color', 'default' => '#ffffff'],
            'vision_bg_image' => ['label' => 'Vision Section Background Image', 'type' => 'image', 'default' => 'uploads/images/bd-flag-bg.jpg'],

            // 6. Our Expertise Grid (4 Pillars)
            'exp_1_icon' => ['label' => 'Expertise 1 Icon (Emoji or SVG)', 'type' => 'text', 'default' => '👥'],
            'exp_1_title' => ['label' => 'Expertise 1 Title', 'type' => 'text', 'default' => 'Staffing Solutions'],
            'exp_1_body'  => ['label' => 'Expertise 1 Description', 'type' => 'textarea', 'default' => 'Providing fast, reliable staffing support by connecting businesses with pre-vetted talent.'],

            'exp_2_icon' => ['label' => 'Expertise 2 Icon', 'type' => 'text', 'default' => '📄'],
            'exp_2_title' => ['label' => 'Expertise 2 Title', 'type' => 'text', 'default' => 'Recruitment Services'],
            'exp_2_body'  => ['label' => 'Expertise 2 Description', 'type' => 'textarea', 'default' => 'Our experienced recruiters partner closely with clients to identify and hire top-tier professionals.'],

            'exp_3_icon' => ['label' => 'Expertise 3 Icon', 'type' => 'text', 'default' => '🧮'],
            'exp_3_title' => ['label' => 'Expertise 3 Title', 'type' => 'text', 'default' => 'Payroll & Employer Services'],
            'exp_3_body'  => ['label' => 'Expertise 3 Description', 'type' => 'textarea', 'default' => 'Handling complex payroll processes to ensure full compliance, reduce risk, and simplify administration.'],

            'exp_4_icon' => ['label' => 'Expertise 4 Icon', 'type' => 'text', 'default' => '⚙️'],
            'exp_4_title' => ['label' => 'Expertise 4 Title', 'type' => 'text', 'default' => 'Workforce Management'],
            'exp_4_body'  => ['label' => 'Expertise 4 Description', 'type' => 'textarea', 'default' => 'Offering comprehensive workforce solutions that include oversight, compliance, and transparent reporting.'],

            // 7. Culture Section
            'culture_heading' => ['label' => 'Culture Section Heading', 'type' => 'text', 'default' => 'Opportunities with NSG Bangladesh'],
            'culture_body' => ['label' => 'Culture Section Description', 'type' => 'textarea', 'default' => 'At NSG Bangladesh, we are building more than just an offshore support team. We are creating a place where talented professionals can grow, contribute, and be part of something bigger.'],
            'office_wall_image' => ['label' => 'Office Showcase Image', 'type' => 'image', 'default' => 'assets/office-wall.png']
        ]
    ],
    'about' => [
        'title' => 'About Us Page',
        'sections' => [
            // 1. Hero Section
            'about_heading' => ['label' => 'Hero Main Heading Title', 'type' => 'text', 'default' => 'About Noor Staffing Group'],
            'about_heading_color' => ['label' => 'Hero Heading Text Color', 'type' => 'color', 'default' => '#111111'],
            'about_heading_size' => ['label' => 'Hero Heading Font Size', 'type' => 'text', 'default' => '3rem'],
            'about_intro' => ['label' => 'Hero Introductory Copy', 'type' => 'textarea', 'default' => 'Bridging talent gaps and delivering offshore operational excellence.'],
            'about_intro_color' => ['label' => 'Hero Intro Text Color', 'type' => 'color', 'default' => '#555555'],
            'about_intro_size' => ['label' => 'Hero Intro Font Size', 'type' => 'text', 'default' => '1.15rem'],

            // 2. Company Story & Main Image
            'about_story_title' => ['label' => 'Company Story Section Title', 'type' => 'text', 'default' => 'Our Journey & Strategic Value'],
            'about_story_title_color' => ['label' => 'Company Story Title Color', 'type' => 'color', 'default' => '#111111'],
            'about_story_body' => ['label' => 'Company Story Main Text Content', 'type' => 'textarea', 'default' => 'Noor Staffing Group in Bangladesh operates as a vital strategic extension of our US operations, providing high-value staffing and operational solutions.'],
            'about_story_body_color' => ['label' => 'Company Story Body Text Color', 'type' => 'color', 'default' => '#444444'],
            'about_page_image' => ['label' => 'Company Story Feature Image', 'type' => 'image', 'default' => 'assets/dhaka-city.jpg'],

            // 3. Senior Management Section Header
            'management_section_title' => ['label' => 'Senior Management Section Header', 'type' => 'text', 'default' => 'Message From Senior Management'],
            'management_section_title_color' => ['label' => 'Senior Management Header Color', 'type' => 'color', 'default' => '#111111'],

            // 4. Mission & Vision Statements
            'about_mission' => ['label' => 'Our Mission Statement', 'type' => 'textarea', 'default' => 'To bridge talent gaps with high-value placement solutions across Bangladesh.'],
            'about_mission_color' => ['label' => 'Mission Copy Text Color', 'type' => 'color', 'default' => '#444444'],
            'about_vision' => ['label' => 'Our Vision Statement', 'type' => 'textarea', 'default' => 'To be the leading talent acquisition partner built on integrity and excellence.'],
            'about_vision_color' => ['label' => 'Vision Copy Text Color', 'type' => 'color', 'default' => '#444444']
        ]
    ],
    'careers' => [
        'title' => 'Careers Page',
        'sections' => [
            'careers_hero_title' => ['label' => 'Careers Header Title', 'type' => 'text', 'default' => 'Discover Your Next Career Step'],
            'careers_hero_title_color' => ['label' => 'Header Title Text Color', 'type' => 'color', 'default' => '#111111'],
            'careers_hero_title_size' => ['label' => 'Header Title Font Size', 'type' => 'text', 'default' => '3rem'],
            'careers_intro' => ['label' => 'Careers Subtitle / Intro Copy', 'type' => 'textarea', 'default' => 'Browse through open positions across various departments and apply directly online.'],
            'careers_intro_color' => ['label' => 'Intro Copy Text Color', 'type' => 'color', 'default' => '#555555'],
            'careers_intro_size' => ['label' => 'Intro Copy Font Size', 'type' => 'text', 'default' => '1.15rem'],
            'careers_no_jobs_text' => ['label' => 'Empty Results Fallback Text', 'type' => 'text', 'default' => 'No positions match your search criteria. Check back later for new openings.']
        ]
    ],
    'people' => [
        'title' => 'People & Culture Page',
        'sections' => [
            'people_hero_title' => ['label' => 'Page Hero Title', 'type' => 'text', 'default' => 'Our People & Culture'],
            'people_hero_title_color' => ['label' => 'Hero Title Color', 'type' => 'color', 'default' => '#111111'],
            'people_hero_title_size' => ['label' => 'Hero Title Font Size', 'type' => 'text', 'default' => '3rem'],
            'people_intro' => ['label' => 'Culture Introductory Copy', 'type' => 'textarea', 'default' => 'Celebrating the individual stories, employee milestones, and collective memories of our talented team.'],
            'people_intro_color' => ['label' => 'Intro Copy Text Color', 'type' => 'color', 'default' => '#555555'],
            'people_intro_size' => ['label' => 'Intro Copy Font Size', 'type' => 'text', 'default' => '1.15rem'],
            'eom_section_title' => ['label' => 'Employee of Month Section Title', 'type' => 'text', 'default' => 'Employee of the Month Spotlight'],
            'stories_section_title' => ['label' => 'Employee Stories Section Title', 'type' => 'text', 'default' => 'Employee Stories & Achievements'],
            'memories_section_title' => ['label' => 'Memories Gallery Section Title', 'type' => 'text', 'default' => 'Team Activities & Moments']
        ]
    ],
    'board' => [
        'title' => 'Leadership & Board Page',
        'sections' => [
            'board_hero_title' => ['label' => 'Leadership Header Title', 'type' => 'text', 'default' => 'Executive Leadership & Board'],
            'board_hero_title_color' => ['label' => 'Header Title Text Color', 'type' => 'color', 'default' => '#111111'],
            'board_hero_title_size' => ['label' => 'Header Title Font Size', 'type' => 'text', 'default' => '3rem'],
            'board_intro' => ['label' => 'Board Introductory Statement', 'type' => 'textarea', 'default' => 'Guided by experienced leaders dedicated to ethical growth, strategic operational strength, and talent development across Bangladesh and global markets.'],
            'board_intro_color' => ['label' => 'Intro Copy Text Color', 'type' => 'color', 'default' => '#555555'],
            'board_intro_size' => ['label' => 'Intro Copy Font Size', 'type' => 'text', 'default' => '1.15rem']
        ]
    ],
    'contact' => [
        'title' => 'Contact Page',
        'sections' => [
            'contact_hero_title' => ['label' => 'Contact Page Title', 'type' => 'text', 'default' => 'Get In Touch'],
            'contact_hero_title_color' => ['label' => 'Page Title Text Color', 'type' => 'color', 'default' => '#111111'],
            'contact_hero_title_size' => ['label' => 'Page Title Font Size', 'type' => 'text', 'default' => '3rem'],
            'contact_intro' => ['label' => 'Contact Description', 'type' => 'textarea', 'default' => 'Reach out for business inquiries, staffing partnerships, or general questions.'],
            'contact_intro_color' => ['label' => 'Intro Copy Text Color', 'type' => 'color', 'default' => '#555555'],
            'contact_intro_size' => ['label' => 'Intro Copy Font Size', 'type' => 'text', 'default' => '1.15rem'],
            'contact_success_msg' => ['label' => 'Form Submission Success Message', 'type' => 'text', 'default' => 'Thank you! Your message has been sent successfully. We will get back to you shortly.']
        ]
    ],
    'apply' => [
        'title' => 'Candidate Application Page',
        'sections' => [
            'apply_hero_title' => ['label' => 'Application Form Header', 'type' => 'text', 'default' => 'Submit Your Application'],
            'apply_hero_title_color' => ['label' => 'Header Text Color', 'type' => 'color', 'default' => '#111111'],
            'apply_hero_title_size' => ['label' => 'Header Font Size', 'type' => 'text', 'default' => '2.75rem'],
            'apply_intro' => ['label' => 'Form Instructions Text', 'type' => 'textarea', 'default' => 'Please upload your latest CV/Resume (PDF or DOCX) and fill out the details below.'],
            'apply_intro_color' => ['label' => 'Instructions Text Color', 'type' => 'color', 'default' => '#555555'],
            'apply_intro_size' => ['label' => 'Instructions Font Size', 'type' => 'text', 'default' => '1.1rem'],
            'apply_success_msg' => ['label' => 'Application Success Confirmation Text', 'type' => 'text', 'default' => 'Application submitted successfully! Our talent acquisition team will review your CV.']
        ]
    ]
];

// Ensure valid page selected
if (!isset($editablePages[$selectedSlug])) {
    $selectedSlug = 'index';
}

$activePageMeta = $editablePages[$selectedSlug];

// --- POST HANDLER: Save Dynamic Page Sections, Uploads & Page Metadata ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        db()->beginTransaction();

        // 1. Update Core Page Table Entry (SEO Title, SEO Description, Status)
        $seoTitle       = trim($_POST['seo_title'] ?? '');
        $seoDescription = trim($_POST['seo_description'] ?? '');
        $pageStatus     = $_POST['status'] ?? 'published';

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
            $activePageMeta['title'],
            $selectedSlug,
            $seoTitle,
            $seoDescription,
            $pageStatus,
            $admin['id']
        ]);

        // 2. Update Component Sections in `page_sections`
        $qSection = db()->prepare('
            INSERT INTO page_sections (page_slug, section_key, content_type, content_value) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_at = NOW()
        ');

        foreach ($activePageMeta['sections'] as $secKey => $secConf) {
            $val = '';

            if ($secConf['type'] === 'image') {
                $uploadedPath = handle_section_image_upload('file_' . $secKey);
                if ($uploadedPath !== null) {
                    $val = $uploadedPath;
                } else {
                    $val = trim((string)($_POST['sec_' . $secKey] ?? ''));
                }
            } else {
                $val = trim((string)($_POST['sec_' . $secKey] ?? ''));
            }

            $qSection->execute([$selectedSlug, $secKey, $secConf['type'], $val]);
        }

        db()->commit();

        log_activity('updated_page_content', 'pages', null, ['page_slug' => $selectedSlug]);
        $message = 'Page content, text colors, font sizes, and images saved successfully!';
    } catch (Throwable $t) {
        db()->rollBack();
        $error = 'Failed to save page sections: ' . $t->getMessage();
    }
}

// --- FETCH EXISTING VALUES ---
$pageRecord = null;
try {
    $q = db()->prepare('SELECT * FROM pages WHERE slug = ? LIMIT 1');
    $q->execute([$selectedSlug]);
    $pageRecord = $q->fetch();
} catch (Throwable $t) {
    // Missing record fallback
}

$sectionValues = [];
try {
    $q = db()->prepare('SELECT section_key, content_value FROM page_sections WHERE page_slug = ?');
    $q->execute([$selectedSlug]);
    $rows = $q->fetchAll();
    foreach ($rows as $r) {
        $sectionValues[$r['section_key']] = $r['content_value'];
    }
} catch (Throwable $t) {
    // Empty sections
}

$pageTitle = 'Dynamic Page Content & Style Editor';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(setting('site_name', 'Noor Staffing')) ?></title>
    <link rel="stylesheet" href="<?= e(site_url('assets/style.css')) ?>">
    <?= render_dynamic_css() ?>
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            background: #123B5D;
            color: #fff;
            padding: 1.5rem 1rem;
            flex-shrink: 0;
        }

        .admin-sidebar h2 {
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.75rem;
        }

        .admin-sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .admin-sidebar-nav a {
            color: #cfd8dc;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .admin-sidebar-nav a:hover,
        .admin-sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .admin-main {
            flex: 1;
            padding: 2rem;
            background: #f4f6f8;
            overflow-y: auto;
        }

        .page-tabs {
            display: flex;
            gap: 0.5rem;
            border-bottom: 2px solid #ddd;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .page-tabs a {
            padding: 0.75rem 1.25rem;
            text-decoration: none;
            color: #555;
            font-weight: 600;
            border-radius: 6px 6px 0 0;
            background: #e0e0e0;
        }

        .page-tabs a.active {
            background: var(--primary-color);
            color: #fff;
        }

        .card-panel {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .card-panel h2 {
            margin-top: 0;
            font-size: 1.25rem;
            color: var(--primary-color);
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
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
            padding: 2px;
            border: 1px solid #ccc;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2><?= e(setting('site_name', 'Noor Staffing')) ?></h2>
            <nav class="admin-sidebar-nav">
                <a href="<?= site_url('admin/index.php') ?>">📊 Dashboard</a>
                <a href="<?= site_url('admin/content/pages.php') ?>" class="active">📄 Dynamic Pages</a>
                <?php if (has_permission('*') || has_permission('jobs.write')): ?>
                    <a href="<?= site_url('admin/careers/jobs.php') ?>">💼 Job Listings</a>
                <?php endif; ?>
                <?php if (has_permission('*') || has_permission('applications.read')): ?>
                    <a href="<?= site_url('admin/careers/applications.php') ?>">📥 Applications</a>
                <?php endif; ?>
                <a href="<?= site_url('admin/people/people.php') ?>">👥 People & Culture</a>
                <a href="<?= site_url('admin/system/messages.php') ?>">✉️ Contact Messages</a>
                <a href="<?= site_url('admin/system/settings.php') ?>">⚙️ Settings & Theme</a>
                <a href="<?= site_url('admin/auth/sign-out.php') ?>" style="margin-top: 2rem; color: #ff8a80;">🚪 Sign Out</a>
            </nav>
        </aside>

        <main class="admin-main">
            <h1>Dynamic Page Content & Style Editor</h1>
            <p class="muted">Manage titles, intros, colors, font sizes, image paths, and SEO per page.</p>

            <?php if ($message): ?>
                <p style="color: #2e7d32; background: #e8f5e9; padding: 0.75rem; border-radius: 4px;"><?= e($message) ?></p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p style="color: #c62828; background: #ffebee; padding: 0.75rem; border-radius: 4px;"><?= e($error) ?></p>
            <?php endif; ?>

            <!-- Page Selector Tabs -->
            <div class="page-tabs">
                <?php foreach ($editablePages as $slug => $meta): ?>
                    <a href="?page=<?= e($slug) ?>" class="<?= $selectedSlug === $slug ? 'active' : '' ?>">
                        <?= e($meta['title']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="post" action="?page=<?= e($selectedSlug) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="card-panel">
                    <h2>📝 <?= e($activePageMeta['title']) ?> Section Parameters</h2>

                    <?php foreach ($activePageMeta['sections'] as $secKey => $secConf): ?>
                        <?php $currentVal = $sectionValues[$secKey] ?? $secConf['default']; ?>
                        <div class="form-group">
                            <label for="sec_<?= e($secKey) ?>"><?= e($secConf['label']) ?></label>

                            <?php if ($secConf['type'] === 'textarea'): ?>
                                <textarea name="sec_<?= e($secKey) ?>" id="sec_<?= e($secKey) ?>" rows="3"><?= e($currentVal) ?></textarea>

                            <?php elseif ($secConf['type'] === 'color'): ?>
                                <div class="color-picker-wrap">
                                    <input type="color" value="<?= e($currentVal) ?>" onchange="document.getElementById('sec_<?= e($secKey) ?>').value = this.value">
                                    <input type="text" name="sec_<?= e($secKey) ?>" id="sec_<?= e($secKey) ?>" value="<?= e($currentVal) ?>">
                                </div>

                            <?php elseif ($secConf['type'] === 'image'): ?>
                                <input type="text" name="sec_<?= e($secKey) ?>" id="sec_<?= e($secKey) ?>" value="<?= e($currentVal) ?>" placeholder="uploads/images/example.jpg">
                                <label style="font-size: 0.8rem; font-weight: normal; margin-top: 0.3rem;">Or upload new image file:</label>
                                <input type="file" name="file_<?= e($secKey) ?>" accept="image/*">

                            <?php else: ?>
                                <input type="text" name="sec_<?= e($secKey) ?>" id="sec_<?= e($secKey) ?>" value="<?= e($currentVal) ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card-panel">
                    <h2>🔍 SEO Metadata & Page Settings</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="seo_title">SEO Browser Title Target</label>
                            <input type="text" name="seo_title" id="seo_title" value="<?= e($pageRecord['seo_title'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="status">Page Publication Status</label>
                            <select name="status" id="status">
                                <option value="published" <?= ($pageRecord['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= ($pageRecord['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="seo_description">SEO Meta Description</label>
                        <textarea name="seo_description" id="seo_description" rows="2"><?= e($pageRecord['seo_description'] ?? '') ?></textarea>
                    </div>
                </div>

                <p style="margin-top: 1.5rem;">
                    <button type="submit" class="button" style="padding: 0.85rem 2rem; font-size: 1rem;">Save Page Changes</button>
                </p>
            </form>
        </main>
    </div>

</body>

</html>
<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Home';

// 1. Dynamic Hero Section Fields
$heroHeading    = page_section('index', 'hero_title', 'Operational excellence with global impact');
$heroColor      = page_section('index', 'hero_title_color', '#ffffff');
$heroFontSize   = page_section('index', 'hero_title_size', '3.5rem');
$heroImg        = page_section('index', 'hero_image', 'assets/hero-city.jpg');

// 2. Dynamic About Section Fields
$aboutTitle     = page_section('index', 'home_about_heading', 'Noor Staffing Group in Bangladesh');
$aboutBody      = page_section('index', 'home_about_body', 'Noor Staffing Group, part of the Noor, Inc. group of companies, supports its nationwide US staffing and workforce solutions through a dedicated offshore support team based in Bangladesh.');
$aboutImg       = page_section('index', 'about_section_image', 'assets/dhaka-city.jpg');

// 3. Dynamic CTA Banner Fields
$ctaHeading     = page_section('index', 'cta_heading', 'Take the first step toward your dreams.');
$ctaBtnLabel    = page_section('index', 'cta_button_label', 'SUBMIT YOUR RESUME');

// 4. Dynamic Stats (1-5)
$stats = [
    page_section('index', 'stat_1', '20+ Years in operation'),
    page_section('index', 'stat_2', '14 US offices supporting clients in 46 states'),
    page_section('index', 'stat_3', '250+ corporate staff in the U.S. and 100+ team members in Bangladesh'),
    page_section('index', 'stat_4', '22 specialized brands under the Noor, Inc. portfolio'),
    page_section('index', 'stat_5', '6,000+ contingent workers actively employed nationwide')
];

// 5. Dynamic Vision Banner Fields
$visionText     = page_section('index', 'vision_quote', 'Driven by talent and built on trust.');
$visionBgImg    = page_section('index', 'vision_bg_image', 'uploads/images/bd-flag-bg.jpg');

// 6. Dynamic Expertise Pillars (4 Cards)
$expertise = [
    ['icon' => page_section('index', 'exp_1_icon', '👥'), 'title' => page_section('index', 'exp_1_title', 'Staffing Solutions'), 'body' => page_section('index', 'exp_1_body', 'Providing fast, reliable staffing support by connecting businesses with pre-vetted talent.')],
    ['icon' => page_section('index', 'exp_2_icon', '📄'), 'title' => page_section('index', 'exp_2_title', 'Recruitment Services'), 'body' => page_section('index', 'exp_2_body', 'Our experienced recruiters partner closely with clients to identify and hire top-tier professionals.')],
    ['icon' => page_section('index', 'exp_3_icon', '🧮'), 'title' => page_section('index', 'exp_3_title', 'Payroll & Employer Services'), 'body' => page_section('index', 'exp_3_body', 'Handling complex payroll processes to ensure full compliance, reduce risk, and simplify administration.')],
    ['icon' => page_section('index', 'exp_4_icon', '⚙️'), 'title' => page_section('index', 'exp_4_title', 'Workforce Management'), 'body' => page_section('index', 'exp_4_body', 'Offering comprehensive workforce solutions that include oversight, compliance, and transparent reporting.')]
];

// 7. Dynamic Culture & Office Section
$cultureHeading = page_section('index', 'culture_heading', 'Opportunities with NSG Bangladesh');
$cultureBody    = page_section('index', 'culture_body', 'At NSG Bangladesh, we are building more than just an offshore support team. We are creating a place where talented professionals can grow, contribute, and be part of something bigger.');
$officeImg      = page_section('index', 'office_wall_image', 'assets/office-wall.png');

// Fetch Executive Leader
$leader = null;
try {
    $qLeader = db()->query("SELECT * FROM board_members WHERE status='published' ORDER BY sort_order ASC LIMIT 1");
    $leader = $qLeader->fetch();
} catch (Throwable $t) {
}

// Fetch Featured Published Jobs
$featuredJobs = [];
try {
    $qJobs = db()->query("SELECT id, title, slug, location, employment_type, department, summary FROM jobs WHERE status='published' AND (closing_date IS NULL OR closing_date >= CURDATE()) ORDER BY is_featured DESC, published_at DESC LIMIT 3");
    $featuredJobs = $qJobs->fetchAll();
} catch (Throwable $t) {
}

require __DIR__ . '/app/header.php';
?>

<!-- 1. Split Hero Section -->
<section class="nsg-hero-split">
    <div class="nsg-hero-left">
        <img src="<?= e(site_url($heroImg)) ?>" alt="Cityscape" class="nsg-hero-img">
    </div>
    <div class="nsg-hero-right">
        <h1 class="nsg-hero-title" style="color: <?= e($heroColor) ?>; font-size: <?= e($heroFontSize) ?>;">
            <?= e($heroHeading) ?>
            <span class="nsg-red-underline"></span>
        </h1>
    </div>
</section>

<!-- 2. About Section -->
<section class="nsg-section nsg-bg-light">
    <div class="nsg-container nsg-grid-2">
        <div class="nsg-content-block">
            <h2 class="nsg-heading-serif"><?= e($aboutTitle) ?></h2>
            <div class="nsg-prose">
                <p><?= nl2br(e($aboutBody)) ?></p>
            </div>
            <a href="<?= e(site_url('about.php')) ?>" class="nsg-link-more">Learn More About Us →</a>
        </div>
        <div class="nsg-image-block">
            <img src="<?= e(site_url($aboutImg)) ?>" alt="Bangladesh Skyline" class="nsg-responsive-img">
        </div>
    </div>
</section>

<!-- 3. CTA Banner -->
<section class="nsg-cta-banner">
    <div class="nsg-container text-center">
        <h2 class="nsg-heading-serif"><?= e($ctaHeading) ?></h2>
        <div style="margin-top: 1.5rem;">
            <a href="<?= e(site_url('careers.php')) ?>" class="nsg-btn-red"><?= e($ctaBtnLabel) ?></a>
        </div>
    </div>
</section>

<!-- 4. Leadership & At A Glance -->
<section class="nsg-section nsg-bg-white">
    <div class="nsg-container nsg-grid-2">
        <div class="nsg-leadership-block">
            <div class="nsg-leader-card">
                <div class="nsg-leader-avatar">
                    <img src="<?= e(site_url($leader['photo_path'] ?? 'assets/leader-placeholder.jpg')) ?>" alt="<?= e($leader['full_name'] ?? 'Leadership') ?>">
                </div>
                <div class="nsg-leader-info">
                    <h3 class="nsg-heading-serif-sm">Leadership</h3>
                    <p class="nsg-leader-name"><strong><?= e($leader['full_name'] ?? 'Mr. Noor') ?></strong></p>
                    <p class="nsg-text-sm"><?= e($leader['biography'] ?? 'Leading Noor Staffing Group with a commitment to operational excellence.') ?></p>
                </div>
            </div>
        </div>

        <div class="nsg-glance-block">
            <h3 class="nsg-heading-serif-sm" style="color: #b71c1c;">NSG at a Glance</h3>
            <ul class="nsg-stats-list">
                <?php foreach ($stats as $st): ?>
                    <?php if (!empty($st)): ?>
                        <li><?= e($st) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<!-- 5. Vision Banner -->
<section class="nsg-vision-banner" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?= e(site_url($visionBgImg)) ?>');">
    <div class="nsg-vision-overlay">
        <h2 class="nsg-vision-text"><?= e($visionText) ?></h2>
    </div>
</section>

<!-- 6. Our Expertise Grid -->
<section class="nsg-section nsg-bg-light text-center">
    <div class="nsg-container">
        <h2 class="nsg-heading-serif" style="margin-bottom: 3rem;">Our Expertise</h2>

        <div class="nsg-grid-4">
            <?php foreach ($expertise as $exp): ?>
                <div class="nsg-expertise-card">
                    <div class="nsg-icon"><?= e($exp['icon']) ?></div>
                    <h4><?= e($exp['title']) ?></h4>
                    <p><?= e($exp['body']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 7. Opportunities with NSG Bangladesh -->
<section class="nsg-section nsg-bg-white">
    <div class="nsg-container nsg-grid-2">
        <div class="nsg-office-img-wrap">
            <img src="<?= e(site_url($officeImg)) ?>" alt="NSG Office Wall" class="nsg-responsive-img shadow">
        </div>
        <div class="nsg-content-block">
            <h2 class="nsg-heading-serif"><?= e($cultureHeading) ?></h2>
            <div class="nsg-prose">
                <p><?= nl2br(e($cultureBody)) ?></p>
            </div>
            <a href="<?= e(site_url('careers.php')) ?>" class="nsg-btn-red" style="margin-top: 1.5rem; display: inline-block;">LEARN MORE</a>
        </div>
    </div>
</section>

<!-- 8. Dynamic Featured Job Openings -->
<section class="nsg-section nsg-bg-light">
    <div class="nsg-container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
            <div>
                <span class="nsg-eyebrow">CAREERS</span>
                <h2 class="nsg-heading-serif" style="margin-top: 0.5rem;">Featured Job Openings</h2>
            </div>
            <a href="<?= e(site_url('careers.php')) ?>" class="nsg-link-more">View All Openings →</a>
        </div>

        <div class="nsg-grid-3">
            <?php foreach ($featuredJobs as $job): ?>
                <div class="nsg-job-card">
                    <span class="nsg-badge-type"><?= e($job['employment_type']) ?></span>
                    <h3><?= e($job['title']) ?></h3>
                    <p class="nsg-job-loc">📍 <?= e($job['location']) ?> <?= !empty($job['department']) ? '• ' . e($job['department']) : '' ?></p>
                    <p class="nsg-job-summary"><?= e($job['summary']) ?></p>
                    <a href="<?= e(site_url('job.php?slug=' . rawurlencode($job['slug']))) ?>" class="nsg-job-link">Apply Position →</a>
                </div>
            <?php endforeach; ?>

            <?php if (empty($featuredJobs)): ?>
                <div class="nsg-job-card" style="grid-column: span 3; text-align: center;">
                    <p>No active job listings published at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/app/footer.php'; ?>
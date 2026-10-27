<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$pageTitle = __t('Careers', 'ক্যারিয়ার');

// 1. Dynamic Hero Settings
$careersTitle      = page_section('careers', 'careers_hero_title', 'Discover Your Next Career Step');
$careersTitleColor = page_section('careers', 'careers_hero_title_color', '#111111');
$careersTitleSize  = page_section('careers', 'careers_hero_title_size', '3rem');

$careersIntro      = page_section('careers', 'careers_intro', 'Browse through open positions across various departments and apply directly online.');
$careersIntroColor = page_section('careers', 'careers_intro_color', '#555555');
$careersIntroSize  = page_section('careers', 'careers_intro_size', '1.15rem');

$noJobsText        = page_section('careers', 'careers_no_jobs_text', 'No positions match your search criteria. Check back later for new openings.');

// Search & Filter Parameters
$searchTerm = trim($_GET['q'] ?? '');
$category   = trim($_GET['category'] ?? '');

// 2. Fetch Active Jobs
$jobsList = [];
try {
    $sql = "SELECT * FROM jobs WHERE status='published'";
    $params = [];

    if ($searchTerm !== '') {
        $sql .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ?)";
        $term = '%' . $searchTerm . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    if ($category !== '') {
        $sql .= " AND department = ?";
        $params[] = $category;
    }

    $sql .= " ORDER BY is_featured DESC, created_at DESC";

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $jobsList = $stmt->fetchAll();
} catch (Throwable $t) {
    $jobsList = [];
}

require __DIR__ . '/app/header.php';
?>

<!-- Hero Banner -->
<section class="nsg-section nsg-bg-white text-center" style="padding-top: 4.5rem; padding-bottom: 3rem; border-bottom: 1px solid #f0f0f0;">
    <div class="nsg-container">
        <span class="nsg-eyebrow"><?= __t('JOIN OUR TEAM', 'আমাদের টিমে যোগ দিন') ?></span>
        <h1 class="nsg-heading-serif" style="font-size: <?= e($careersTitleSize) ?>; color: <?= e($careersTitleColor) ?>; margin-top: 0.5rem; margin-bottom: 1rem;">
            <?= e(get_localized(['title' => $careersTitle], 'title')) ?>
        </h1>
        <p style="margin: 0 auto; font-size: <?= e($careersIntroSize) ?>; color: <?= e($careersIntroColor) ?>; max-width: 760px; line-height: 1.7;">
            <?= e(get_localized(['intro' => $careersIntro], 'intro')) ?>
        </p>
    </div>
</section>

<!-- Search & Filter Bar -->
<section style="background: #fafafa; padding: 2rem 0; border-bottom: 1px solid #eee;">
    <div class="nsg-container">
        <form method="get" action="careers.php" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
            <input type="text" name="q" value="<?= e($searchTerm) ?>" placeholder="<?= __t('Search by role, keyword, or location...', 'পদ বা কিওয়ার্ড দিয়ে খুঁজুন...') ?>" style="flex: 2; min-width: 240px; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px;">
            <button type="submit" class="nsg-btn-red" style="padding: 0.8rem 1.75rem; border: none; background: #b71c1c; color: #fff; font-weight: 700; border-radius: 4px; cursor: pointer;"><?= __t('SEARCH JOBS', 'খুঁজুন') ?></button>
            <?php if ($searchTerm !== '' || $category !== ''): ?>
                <a href="careers.php" style="color: #666; font-size: 0.9rem; text-decoration: underline;"><?= __t('Clear Filters', 'ফিল্টার মুছুন') ?></a>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- Jobs Grid -->
<section class="nsg-section nsg-bg-light" style="padding: 4.5rem 0;">
    <div class="nsg-container">
        <?php if (!empty($jobsList)): ?>
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <?php foreach ($jobsList as $job): ?>
                    <div style="background: #fff; padding: 2rem; border-radius: 8px; border: 1px solid #eef2f5; box-shadow: 0 2px 8px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                        <div style="flex: 1; min-width: 280px;">
                            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
                                <span style="background: #ffebee; color: #b71c1c; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 4px; text-transform: uppercase;"><?= e($job['type'] ?? 'Full Time') ?></span>
                                <?php if (!empty($job['department'])): ?>
                                    <span style="color: #777; font-size: 0.85rem;">• <?= e($job['department']) ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 style="font-family: Playfair Display, serif; font-size: 1.5rem; margin: 0 0 0.5rem; color: #111;"><?= e(get_localized($job, 'title', 'jobs', (int)$job['id'])) ?></h3>
                            <p style="color: #666; font-size: 0.95rem; margin: 0 0 0.75rem;">📍 <?= e($job['location'] ?? 'Dhaka, Bangladesh') ?></p>
                            <p style="color: #444; font-size: 0.95rem; margin: 0; line-height: 1.6;"><?= e(substr(strip_tags($job['description']), 0, 180)) ?>...</p>
                        </div>
                        <div>
                            <a href="<?= e(site_url('apply.php?job_id=' . (int)$job['id'])) ?>" class="nsg-btn-red" style="padding: 0.75rem 1.5rem; background: #b71c1c; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; display: inline-block;"><?= __t('APPLY NOW', 'আবেদন করুন') ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="background: #fff; padding: 4rem 2rem; border-radius: 8px; text-align: center;">
                <h3 style="font-family: Playfair Display, serif; font-size: 1.5rem; margin-bottom: 0.5rem;"><?= __t('No Openings Found', 'কোনো খালি পদ পাওয়া যায়নি') ?></h3>
                <p style="color: #666; max-width: 500px; margin: 0 auto 1.5rem;"><?= e(get_localized(['msg' => $noJobsText], 'msg')) ?></p>
                <a href="<?= e(site_url('apply.php')) ?>" class="nsg-btn-red" style="padding: 0.75rem 1.5rem; background: #123B5D; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 700;"><?= __t('SUBMIT GENERAL APPLICATION', 'সাধারণ সিভি জমা দিন') ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/app/footer.php'; ?>
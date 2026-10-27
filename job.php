<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$jobSlug = trim($_GET['slug'] ?? '');
$jobId   = (int)($_GET['id'] ?? 0);

$job = null;

try {
    if ($jobSlug !== '') {
        $stmt = db()->prepare("SELECT * FROM jobs WHERE slug = ? AND status = 'published' LIMIT 1");
        $stmt->execute([$jobSlug]);
        $job = $stmt->fetch();
    } elseif ($jobId > 0) {
        $stmt = db()->prepare("SELECT * FROM jobs WHERE id = ? AND status = 'published' LIMIT 1");
        $stmt->execute([$jobId]);
        $job = $stmt->fetch();
    }
} catch (Throwable $t) {
    $job = null;
}

// Render 404 state if job circular doesn't exist or isn't published
if (!$job) {
    $pageTitle = __t('Job Not Found', 'বিজ্ঞপ্তিটি পাওয়া যায়নি');
    require __DIR__ . '/app/header.php';
?>
    <section class="nsg-section nsg-bg-light text-center" style="padding: 6rem 0;">
        <div class="nsg-container">
            <h1 class="nsg-heading-serif" style="font-size: 2.5rem; margin-bottom: 1rem;"><?= e($pageTitle) ?></h1>
            <p style="color: #666; margin-bottom: 2rem;"><?= __t('The job vacancy you are looking for may have expired or been removed.', 'আপনার অনুসন্ধানের সার্কুলারটির মেয়াদ শেষ হয়েছে বা মুছে ফেলা হয়েছে।') ?></p>
            <a href="<?= e(site_url('careers.php')) ?>" class="nsg-btn-red" style="padding: 0.85rem 1.75rem; background: #b71c1c; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 700; display: inline-block;"><?= __t('EXPLORE OTHER OPENINGS', 'অন্যান্য চাকরি দেখুন') ?></a>
        </div>
    </section>
<?php
    require __DIR__ . '/app/footer.php';
    exit;
}

$pageTitle = get_localized($job, 'title', 'jobs', (int)$job['id']);

require __DIR__ . '/app/header.php';
?>

<!-- Job Header Banner -->
<section class="nsg-section nsg-bg-white" style="padding-top: 4.5rem; padding-bottom: 3rem; border-bottom: 1px solid #f0f0f0;">
    <div class="nsg-container">
        <a href="<?= e(site_url('careers.php')) ?>" style="color: #b71c1c; font-size: 0.9rem; font-weight: 700; text-decoration: none; display: inline-block; margin-bottom: 1rem;">← <?= __t('BACK TO CAREERS', 'ক্যারিয়ার পাতায় ফিরুন') ?></a>

        <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.75rem;">
            <span style="background: #ffebee; color: #b71c1c; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 4px; text-transform: uppercase;"><?= e($job['employment_type'] ?? $job['type'] ?? 'Full Time') ?></span>
            <?php if (!empty($job['department'])): ?>
                <span style="color: #777; font-size: 0.85rem;">• <?= e($job['department']) ?></span>
            <?php endif; ?>
        </div>

        <h1 class="nsg-heading-serif" style="font-size: 2.75rem; color: #111; margin-bottom: 0.75rem;">
            <?= e($pageTitle) ?>
        </h1>

        <div style="display: flex; gap: 1.5rem; color: #555; font-size: 0.95rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
            <span>📍 <strong><?= __t('Location', 'অবস্থান') ?>:</strong> <?= e($job['location'] ?? 'Dhaka, Bangladesh') ?></span>
            <?php if (!empty($job['closing_date'])): ?>
                <span>📅 <strong><?= __t('Deadline', 'আবেদনের শেষ তারিখ') ?>:</strong> <?= e(date('M d, Y', strtotime($job['closing_date']))) ?></span>
            <?php endif; ?>
        </div>

        <a href="<?= e(site_url('apply.php?job_id=' . (int)$job['id'])) ?>" class="nsg-btn-red" style="padding: 0.85rem 2rem; background: #b71c1c; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 700; display: inline-block; text-transform: uppercase;"><?= __t('APPLY FOR THIS POSITION', 'এই পদে আবেদন করুন') ?></a>
    </div>
</section>

<!-- Job Detail Body & Qualifications -->
<section class="nsg-section nsg-bg-light" style="padding: 4rem 0;">
    <div class="nsg-container" style="max-width: 860px;">
        <div style="background: #fff; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); border: 1px solid #eef2f5;">

            <?php if (!empty($job['summary'])): ?>
                <div style="background: #fafafa; padding: 1.25rem 1.5rem; border-left: 4px solid #b71c1c; margin-bottom: 2rem; border-radius: 4px;">
                    <strong style="color: #111; font-size: 0.95rem; display: block; margin-bottom: 0.25rem;"><?= __t('Role Overview', 'সংক্ষিপ্ত সারসংক্ষেপ') ?>:</strong>
                    <p style="margin: 0; color: #555; font-size: 0.95rem; line-height: 1.6;"><?= nl2br(e($job['summary'])) ?></p>
                </div>
            <?php endif; ?>

            <h3 style="font-family: Playfair Display, serif; font-size: 1.5rem; color: #111; margin-bottom: 1rem; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem;"><?= __t('Job Description & Responsibilities', 'চাকরির বিবরণ ও দায়িত্বসমূহ') ?></h3>

            <div class="nsg-prose" style="line-height: 1.8; color: #444; font-size: 1rem; margin-bottom: 2.5rem;">
                <?= nl2br(e(get_localized($job, 'description', 'jobs', (int)$job['id']))) ?>
            </div>

            <?php if (!empty($job['requirements'])): ?>
                <h3 style="font-family: Playfair Display, serif; font-size: 1.5rem; color: #111; margin-bottom: 1rem; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem;"><?= __t('Requirements & Qualifications', 'যোগ্যতা ও প্রয়োজনীয়তা') ?></h3>
                <div class="nsg-prose" style="line-height: 1.8; color: #444; font-size: 1rem; margin-bottom: 2.5rem;">
                    <?= nl2br(e(get_localized($job, 'requirements', 'jobs', (int)$job['id']))) ?>
                </div>
            <?php endif; ?>

            <div style="border-top: 1px solid #eee; padding-top: 2rem; text-align: center;">
                <h4 style="font-family: Playfair Display, serif; font-size: 1.35rem; margin-bottom: 1rem; color: #111;"><?= __t('Interested in this opportunity?', 'এই পদের জন্য আগ্রহী?') ?></h4>
                <a href="<?= e(site_url('apply.php?job_id=' . (int)$job['id'])) ?>" class="nsg-btn-red" style="padding: 0.9rem 2.25rem; background: #b71c1c; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 700; display: inline-block; text-transform: uppercase;"><?= __t('SUBMIT APPLICATION NOW', 'এখনই আবেদন জমা দিন') ?></a>
            </div>

        </div>
    </div>
</section>

<?php require __DIR__ . '/app/footer.php'; ?>
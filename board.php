<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$pageTitle = __t('Leadership & Board', 'নির্বাহী নেতৃত্ব');

// 1. Dynamic Hero Section Settings (Managed via Admin Pages)
$boardTitle      = page_section('board', 'board_hero_title', 'Executive Leadership & Board');
$boardTitleColor = page_section('board', 'board_hero_title_color', '#111111');
$boardTitleSize  = page_section('board', 'board_hero_title_size', '3rem');

$boardIntro      = page_section('board', 'board_intro', 'Guided by experienced leaders dedicated to ethical growth, strategic operational strength, and talent development across Bangladesh and global markets.');
$boardIntroColor = page_section('board', 'board_intro_color', '#555555');
$boardIntroSize  = page_section('board', 'board_intro_size', '1.15rem');

// 2. Fetch Board Members / Leadership Hierarchy from DB
$boardMembers = [];
try {
    $qBoard = db()->query("SELECT * FROM board_members WHERE status='published' ORDER BY sort_order ASC, id ASC");
    $boardMembers = $qBoard->fetchAll();
} catch (Throwable $t) {
    $boardMembers = [];
}

require __DIR__ . '/app/header.php';
?>

<!-- 1. Hero Header Banner -->
<section class="nsg-section nsg-bg-white text-center" style="padding-top: 4.5rem; padding-bottom: 3rem; border-bottom: 1px solid #f0f0f0;">
    <div class="nsg-container">
        <span class="nsg-eyebrow"><?= __t('EXECUTIVE GOVERNANCE', 'পরিচালনা পর্ষদ') ?></span>
        <h1 class="nsg-heading-serif" style="font-size: <?= e($boardTitleSize) ?>; color: <?= e($boardTitleColor) ?>; margin-top: 0.5rem; margin-bottom: 1rem;">
            <?= e(get_localized(['title' => $boardTitle], 'title')) ?>
        </h1>
        <p style="margin: 0 auto; font-size: <?= e($boardIntroSize) ?>; color: <?= e($boardIntroColor) ?>; max-width: 760px; line-height: 1.7;">
            <?= e(get_localized(['intro' => $boardIntro], 'intro')) ?>
        </p>
    </div>
</section>

<!-- 2. Dynamic Leadership Hierarchy Grid -->
<section class="nsg-section nsg-bg-light" style="padding: 4.5rem 0;">
    <div class="nsg-container">
        <?php if (!empty($boardMembers)): ?>
            <div class="nsg-grid-3" style="gap: 2rem;">
                <?php foreach ($boardMembers as $member): ?>
                    <?php
                    $photoSrc = !empty($member['photo_path']) ? site_url($member['photo_path']) : '';
                    if (empty($photoSrc) || (str_starts_with($member['photo_path'] ?? '', 'uploads/') && !file_exists(__DIR__ . '/' . $member['photo_path']))) {
                        $photoSrc = 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=400&q=80';
                    }
                    ?>

                    <div class="nsg-job-card-rich" style="padding: 2.25rem 2rem; text-align: center; background: #fff; border: 1px solid #eef2f5; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); display: flex; flex-direction: column; align-items: center;">

                        <!-- Avatar Circle -->
                        <div style="width: 130px; height: 130px; border-radius: 50%; overflow: hidden; border: 3px solid #b71c1c; margin-bottom: 1.25rem; background: #f0f0f0; flex-shrink: 0;">
                            <img src="<?= e($photoSrc) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </div>

                        <!-- Designation & Name -->
                        <span class="nsg-eyebrow" style="font-size: 0.75rem; color: #b71c1c; font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 1px;"><?= e($member['title']) ?></span>
                        <h3 class="nsg-heading-serif-sm" style="font-size: 1.5rem; margin: 0 0 0.75rem; color: #111;"><?= e($member['full_name']) ?></h3>

                        <?php if (!empty($member['message'])): ?>
                            <blockquote style="margin: 0 0 1rem 0; font-style: italic; color: #333; font-size: 0.95rem; font-family: Playfair Display, serif; line-height: 1.5;">
                                "<?= e(get_localized($member, 'message', 'board_members', (int)$member['id'])) ?>"
                            </blockquote>
                        <?php endif; ?>

                        <p style="font-size: 0.9rem; line-height: 1.6; color: #555; margin: 0; flex: 1;">
                            <?= nl2br(e(get_localized($member, 'biography', 'board_members', (int)$member['id']))) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="nsg-expertise-card text-center" style="padding: 4rem 2rem; background: #fff;">
                <h3 class="nsg-heading-serif-sm"><?= __t('No Leadership Profiles Published', 'কোনো প্রোফাইল প্রকাশিত হয়নি') ?></h3>
                <p style="color: #666; margin-top: 0.5rem;"><?= __t('Please add and publish board members from the admin panel.', 'এডমিন প্যানেল থেকে বোর্ড মেম্বার যুক্ত করুন।') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- 3. Join Vision CTA Banner -->
<section class="nsg-cta-banner" style="background: #000; color: #fff; padding: 4rem 0;">
    <div class="nsg-container text-center">
        <h2 class="nsg-heading-serif" style="color: #fff; font-size: 2.25rem;"><?= __t('Be part of our expanding journey', 'আমাদের পথচলার অংশ হন') ?></h2>
        <div style="margin-top: 1.5rem;">
            <a href="<?= e(site_url('careers.php')) ?>" class="nsg-btn-red"><?= __t('EXPLORE CAREERS AT NSG', 'ক্যারিয়ারের সুযোগ দেখুন') ?></a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/app/footer.php'; ?>
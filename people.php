<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$pageTitle = __t('People & Culture', 'মানুষ ও সংস্কৃতি');

// 1. Dynamic Page Section Copy & Header Styles (Managed via Admin -> Content -> People & Culture)
$peopleTitle      = page_section('people', 'people_hero_title', 'Our People & Culture');
$peopleTitleColor = page_section('people', 'people_hero_title_color', '#111111');
$peopleTitleSize  = page_section('people', 'people_hero_title_size', '3rem');

$peopleIntro      = page_section('people', 'people_intro', 'Celebrating the individual stories, employee milestones, and collective memories of our talented team.');
$peopleIntroColor = page_section('people', 'people_intro_color', '#555555');
$peopleIntroSize  = page_section('people', 'people_intro_size', '1.15rem');

$eomTitle         = page_section('people', 'eom_section_title', 'Employee of the Month Spotlight');
$storiesTitle     = page_section('people', 'stories_section_title', 'Employee Stories & Achievements');
$memoriesTitle    = page_section('people', 'memories_section_title', 'Team Activities & Moments');

// 2. Fetch Published Employee of the Month Records
$eomList = [];
try {
    $qEom = db()->query("SELECT * FROM employee_of_month WHERE status='published' ORDER BY sort_order ASC, id DESC");
    $eomList = $qEom->fetchAll();
} catch (Throwable $t) {
    $eomList = [];
}

// 3. Fetch Published Employee Stories & Posts
$stories = [];
try {
    $qStories = db()->query("SELECT * FROM employee_stories WHERE status='published' ORDER BY is_featured DESC, created_at DESC");
    $stories = $qStories->fetchAll();
} catch (Throwable $t) {
    $stories = [];
}

// 4. Fetch Published Team Activities / Photo Gallery
$memories = [];
try {
    $qMemories = db()->query("SELECT * FROM employee_memories WHERE status='published' ORDER BY event_date DESC, id DESC");
    $memories = $qMemories->fetchAll();
} catch (Throwable $t) {
    $memories = [];
}

require __DIR__ . '/app/header.php';
?>

<!-- 1. Hero Header Banner -->
<section class="nsg-section nsg-bg-white text-center" style="padding-top: 4.5rem; padding-bottom: 3rem; border-bottom: 1px solid #f0f0f0;">
    <div class="nsg-container">
        <span class="nsg-eyebrow"><?= __t('INSIDE NSG BANGLADESH', 'আমাদের সংস্কৃতি') ?></span>
        <h1 class="nsg-heading-serif" style="font-size: <?= e($peopleTitleSize) ?>; color: <?= e($peopleTitleColor) ?>; margin-top: 0.5rem; margin-bottom: 1rem;">
            <?= e(get_localized(['title' => $peopleTitle], 'title')) ?>
        </h1>
        <p style="margin: 0 auto; font-size: <?= e($peopleIntroSize) ?>; color: <?= e($peopleIntroColor) ?>; max-width: 740px; line-height: 1.7;">
            <?= e(get_localized(['intro' => $peopleIntro], 'intro')) ?>
        </p>
    </div>
</section>

<!-- 2. Employee of the Month Spotlight Section -->
<?php if (!empty($eomList)): ?>
    <section class="nsg-section nsg-bg-light" style="padding: 4.5rem 0;">
        <div class="nsg-container">
            <div class="text-center" style="margin-bottom: 3.5rem;">
                <span class="nsg-eyebrow"><?= __t('RECOGNITION & EXCELLENCE', 'স্বীকৃতি') ?></span>
                <h2 class="nsg-heading-serif" style="margin-top: 0.5rem; font-size: 2.25rem;"><?= e($eomTitle) ?></h2>
            </div>

            <div class="nsg-grid-2" style="gap: 2rem;">
                <?php foreach ($eomList as $eom): ?>
                    <?php
                    $eomPhoto = !empty($eom['photo_path']) ? site_url($eom['photo_path']) : '';
                    if (empty($eomPhoto) || (str_starts_with($eom['photo_path'] ?? '', 'uploads/') && !file_exists(__DIR__ . '/' . $eom['photo_path']))) {
                        $eomPhoto = 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80';
                    }
                    ?>
                    <div class="nsg-job-card-rich" style="text-align: left; padding: 2.25rem; display: flex; gap: 1.5rem; align-items: center; background: #fff; border: 1px solid #eef2f5; border-radius: 8px;">
                        <div style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; border: 3px solid #b71c1c; flex-shrink: 0; background: #f0f0f0;">
                            <img src="<?= e($eomPhoto) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1;">
                            <span class="nsg-badge-type" style="margin-bottom: 0.5rem; font-size: 0.75rem; display: inline-block; background: #ffebee; color: #b71c1c; font-weight: 700;"><?= e($eom['month_label']) ?></span>
                            <h3 class="nsg-heading-serif-sm" style="font-size: 1.4rem; margin: 0.25rem 0; color: #111;"><?= e($eom['name']) ?></h3>
                            <p style="color: #b71c1c; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;"><?= e($eom['role']) ?></p>
                            <?php if (!empty($eom['quote'])): ?>
                                <blockquote style="margin: 0; font-style: italic; color: #444; font-size: 0.95rem; border-left: 2px solid #b71c1c; padding-left: 0.75rem; font-family: Playfair Display, serif;">
                                    "<?= e(get_localized($eom, 'quote', 'employee_of_month', (int)$eom['id'])) ?>"
                                </blockquote>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- 3. Employee Stories Section -->
<?php if (!empty($stories)): ?>
    <section class="nsg-section nsg-bg-white" style="padding: 4.5rem 0;">
        <div class="nsg-container">
            <div style="margin-bottom: 3.5rem;" class="text-center">
                <span class="nsg-eyebrow"><?= __t('TEAM JOURNEYS', 'টিমের গল্প') ?></span>
                <h2 class="nsg-heading-serif" style="margin-top: 0.5rem; font-size: 2.25rem;"><?= e($storiesTitle) ?></h2>
            </div>

            <div class="nsg-grid-3" style="gap: 2rem;">
                <?php foreach ($stories as $story): ?>
                    <?php
                    $storyPhoto = !empty($story['cover_image']) ? site_url($story['cover_image']) : '';
                    if (empty($storyPhoto) || (str_starts_with($story['cover_image'] ?? '', 'uploads/') && !file_exists(__DIR__ . '/' . $story['cover_image']))) {
                        $storyPhoto = 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80';
                    }
                    ?>
                    <article class="nsg-job-card-rich" style="padding: 0; overflow: hidden; background: #fff; border: 1px solid #eef2f5; border-radius: 8px; display: flex; flex-direction: column;">
                        <?php if (!empty($storyPhoto)): ?>
                            <div style="height: 200px; width: 100%; overflow: hidden; background: #f0f0f0;">
                                <img src="<?= e($storyPhoto) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <div style="padding: 1.75rem; display: flex; flex-direction: column; flex: 1;">
                            <span class="nsg-eyebrow" style="font-size: 0.75rem; color: #b71c1c; margin-bottom: 0.5rem;"><?= e($story['author_name'] ?? 'Team Member') ?> <?= !empty($story['author_role']) ? '• ' . e($story['author_role']) : '' ?></span>
                            <h3 class="nsg-job-card-title" style="font-size: 1.35rem; margin-bottom: 0.75rem;"><?= e(get_localized($story, 'title', 'employee_stories', (int)$story['id'])) ?></h3>
                            <p class="nsg-job-card-summary" style="font-size: 0.95rem; color: #555; line-height: 1.6; margin-bottom: 1.5rem; flex: 1;">
                                <?= e(get_localized($story, 'excerpt', 'employee_stories', (int)$story['id']) ?: substr(strip_tags($story['body']), 0, 150) . '...') ?>
                            </p>
                            <div style="border-top: 1px solid #f0f0f0; padding-top: 0.75rem; margin-top: auto;">
                                <small style="font-size: 0.8rem; color: #888;"><?= e(date('M d, Y', strtotime($story['published_at'] ?? $story['created_at']))) ?></small>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- 4. Team Activities / Photo Gallery Section -->
<?php if (!empty($memories)): ?>
    <section class="nsg-section nsg-bg-light" style="padding: 4.5rem 0;">
        <div class="nsg-container">
            <div class="text-center" style="margin-bottom: 3.5rem;">
                <span class="nsg-eyebrow"><?= __t('CULTURE & ACTIVITIES', 'গ্যালারি ও কার্যক্রম') ?></span>
                <h2 class="nsg-heading-serif" style="margin-top: 0.5rem; font-size: 2.25rem;"><?= e($memoriesTitle) ?></h2>
            </div>

            <div class="nsg-grid-3" style="gap: 2rem;">
                <?php foreach ($memories as $mem): ?>
                    <?php
                    $memPhoto = !empty($mem['photo_path']) ? site_url($mem['photo_path']) : '';
                    if (empty($memPhoto) || (str_starts_with($mem['photo_path'] ?? '', 'uploads/') && !file_exists(__DIR__ . '/' . $mem['photo_path']))) {
                        $memPhoto = 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=600&q=80';
                    }
                    ?>
                    <div class="nsg-job-card-rich" style="padding: 0; overflow: hidden; border-radius: 8px; background: #fff; border: 1px solid #eef2f5;">
                        <div style="width: 100%; height: 230px; overflow: hidden; background: #f0f0f0;">
                            <img src="<?= e($memPhoto) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </div>
                        <div style="padding: 1.5rem; text-align: left;">
                            <h4 style="font-family: Playfair Display, serif; font-size: 1.2rem; margin: 0 0 0.5rem; color: #111;"><?= e(get_localized($mem, 'title', 'employee_memories', (int)$mem['id'])) ?></h4>
                            <?php if (!empty($mem['event_date'])): ?>
                                <span class="nsg-eyebrow" style="font-size: 0.75rem; color: #b71c1c; display: block; margin-bottom: 0.5rem;"><?= e(date('F Y', strtotime($mem['event_date']))) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($mem['caption'])): ?>
                                <p style="font-size: 0.9rem; color: #555; margin: 0; line-height: 1.6;"><?= e(get_localized($mem, 'caption', 'employee_memories', (int)$mem['id'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/app/footer.php'; ?>
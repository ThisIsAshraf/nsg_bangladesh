<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$pageTitle = __t('About Us', 'আমাদের সম্পর্কে');

// 1. Dynamic Hero Section Settings
$aboutHeading      = page_section('about', 'about_heading', 'About Noor Staffing Group');
$aboutHeadingColor = page_section('about', 'about_heading_color', '#111111');
$aboutHeadingSize  = page_section('about', 'about_heading_size', '3rem');

$aboutIntro        = page_section('about', 'about_intro', 'Bridging talent gaps and delivering offshore operational excellence.');
$aboutIntroColor   = page_section('about', 'about_intro_color', '#555555');
$aboutIntroSize    = page_section('about', 'about_intro_size', '1.15rem');

// 2. Dynamic Company Story & Image
$aboutStoryTitle      = page_section('about', 'about_story_title', 'Our Journey & Strategic Value');
$aboutStoryTitleColor = page_section('about', 'about_story_title_color', '#111111');
$aboutStoryBody       = page_section('about', 'about_story_body', 'Noor Staffing Group in Bangladesh operates as a vital strategic extension of our US operations, providing high-value staffing and operational solutions.');
$aboutStoryBodyColor  = page_section('about', 'about_story_body_color', '#444444');
$aboutStoryImage      = page_section('about', 'about_page_image', 'assets/dhaka-city.jpg');

// 3. Senior Management Section Copy
$managementSecTitle      = page_section('about', 'management_section_title', 'Message From Senior Management');
$managementSecTitleColor = page_section('about', 'management_section_title_color', '#111111');

// 4. Mission & Vision Copies
$aboutMission      = page_section('about', 'about_mission', 'To bridge talent gaps with high-value placement solutions across Bangladesh.');
$aboutMissionColor = page_section('about', 'about_mission_color', '#444444');
$aboutVision       = page_section('about', 'about_vision', 'To be the leading talent acquisition partner built on integrity and excellence.');
$aboutVisionColor  = page_section('about', 'about_vision_color', '#444444');

// 5. Fetch Senior Management Profiles (Dynamically added from Admin Panel)
$seniorManagement = [];
try {
    $qExec = db()->query("SELECT * FROM board_members WHERE status='published' ORDER BY sort_order ASC, id ASC");
    $seniorManagement = $qExec->fetchAll();
} catch (Throwable $t) {
    $seniorManagement = [];
}

require __DIR__ . '/app/header.php';
?>

<!-- Hero Banner -->
<section class="nsg-section nsg-bg-white text-center" style="padding-top: 4.5rem; padding-bottom: 3rem; border-bottom: 1px solid #f0f0f0;">
    <div class="nsg-container">
        <span class="nsg-eyebrow"><?= __t('WHO WE ARE', 'আমাদের পরিচয়') ?></span>
        <h1 class="nsg-heading-serif" style="font-size: <?= e($aboutHeadingSize) ?>; color: <?= e($aboutHeadingColor) ?>; margin-top: 0.5rem; margin-bottom: 1rem;">
            <?= e(get_localized(['heading' => $aboutHeading], 'heading')) ?>
        </h1>
        <p style="margin: 0 auto; font-size: <?= e($aboutIntroSize) ?>; color: <?= e($aboutIntroColor) ?>; max-width: 780px; line-height: 1.7;">
            <?= e(get_localized(['intro' => $aboutIntro], 'intro')) ?>
        </p>
    </div>
</section>

<!-- Company Story & Dynamic Image -->
<section class="nsg-section nsg-bg-light" style="padding: 4rem 0;">
    <div class="nsg-container nsg-grid-2" style="align-items: center; gap: 3rem;">
        <div class="nsg-content-block">
            <span class="nsg-eyebrow"><?= __t('OUR STORY', 'আমাদের গল্প') ?></span>
            <h2 class="nsg-heading-serif" style="color: <?= e($aboutStoryTitleColor) ?>; margin-top: 0.5rem; margin-bottom: 1.5rem; font-size: 2.25rem;">
                <?= e(get_localized(['story_title' => $aboutStoryTitle], 'story_title')) ?>
            </h2>
            <div class="nsg-prose" style="line-height: 1.8; color: <?= e($aboutStoryBodyColor) ?>; font-size: 1.05rem;">
                <p><?= nl2br(e(get_localized(['story_body' => $aboutStoryBody], 'story_body'))) ?></p>
            </div>
        </div>
        <div class="nsg-image-block">
            <?php
            $imgSrc = site_url($aboutStoryImage);
            if (!empty($aboutStoryImage) && str_starts_with($aboutStoryImage, 'uploads/') && !file_exists(__DIR__ . '/' . $aboutStoryImage)) {
                $imgSrc = 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80';
            }
            ?>
            <img src="<?= e($imgSrc) ?>" alt="Company Story Image" class="nsg-responsive-img shadow" style="border-radius: 8px; width: 100%; height: 360px; object-fit: cover;">
        </div>
    </div>
</section>

<!-- Dynamic Messages From Senior Management (CEO, Directors, Executive Leadership) -->
<?php if (!empty($seniorManagement)): ?>
    <section class="nsg-section nsg-bg-white" style="padding: 4.5rem 0;">
        <div class="nsg-container">
            <div class="text-center" style="margin-bottom: 3.5rem;">
                <span class="nsg-eyebrow"><?= __t('LEADERSHIP MESSAGES', 'বার্তা') ?></span>
                <h2 class="nsg-heading-serif" style="color: <?= e($managementSecTitleColor) ?>; margin-top: 0.5rem; font-size: 2.25rem;">
                    <?= e(get_localized(['mgt_title' => $managementSecTitle], 'mgt_title')) ?>
                </h2>
            </div>

            <div style="display: flex; flex-direction: column; gap: 2.5rem;">
                <?php foreach ($seniorManagement as $leader): ?>
                    <div class="nsg-job-card-rich" style="padding: 2.5rem; display: flex; flex-direction: row; gap: 2.5rem; align-items: center; flex-wrap: wrap; background: #fff; border: 1px solid #eef2f5; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">

                        <!-- Uploaded Leader Photo -->
                        <div class="nsg-leader-avatar" style="width: 150px; height: 150px; flex-shrink: 0; margin: 0; border: 3px solid var(--nsg-red, #b71c1c); border-radius: 50%; overflow: hidden; background: #f0f0f0;">
                            <?php
                            $leaderImg = site_url($leader['photo_path'] ?? 'assets/leader-placeholder.jpg');
                            if (!empty($leader['photo_path']) && str_starts_with($leader['photo_path'], 'uploads/') && !file_exists(__DIR__ . '/' . $leader['photo_path'])) {
                                $leaderImg = 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=400&q=80';
                            }
                            ?>
                            <img src="<?= e($leaderImg) ?>" alt="<?= e($leader['full_name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <!-- Name, Title, CEO Quote & Bio -->
                        <div style="flex: 1; min-width: 280px;">
                            <span class="nsg-eyebrow" style="font-size: 0.8rem; color: #b71c1c; font-weight: 700;"><?= e($leader['title']) ?></span>
                            <h3 class="nsg-heading-serif-sm" style="font-size: 1.8rem; margin: 0.3rem 0 1rem; color: #111;"><?= e($leader['full_name']) ?></h3>

                            <?php if (!empty($leader['message'])): ?>
                                <blockquote style="margin: 0 0 1.25rem 0; font-style: italic; color: #222; font-size: 1.05rem; font-family: Playfair Display, serif; border-left: 3px solid #b71c1c; padding-left: 1rem; line-height: 1.6;">
                                    "<?= e(get_localized($leader, 'message', 'board_members', (int)$leader['id'])) ?>"
                                </blockquote>
                            <?php endif; ?>

                            <p style="font-size: 0.95rem; line-height: 1.7; color: #555; margin: 0;">
                                <?= nl2br(e(get_localized($leader, 'biography', 'board_members', (int)$leader['id']))) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Mission & Vision Cards -->
<section class="nsg-section nsg-bg-light" style="padding: 4rem 0;">
    <div class="nsg-container nsg-grid-2" style="gap: 2rem;">
        <div class="nsg-expertise-card" style="text-align: left; padding: 2.5rem; border-left: 4px solid #b71c1c; background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <div class="nsg-icon" style="font-size: 2rem; margin-bottom: 0.75rem;">🎯</div>
            <h3 class="nsg-heading-serif-sm" style="font-size: 1.5rem; margin-bottom: 0.75rem;"><?= __t('Our Mission', 'আমাদের লক্ষ্য') ?></h3>
            <p style="color: <?= e($aboutMissionColor) ?>; font-size: 1.05rem; line-height: 1.6; margin: 0;"><?= nl2br(e(get_localized(['mission' => $aboutMission], 'mission'))) ?></p>
        </div>

        <div class="nsg-expertise-card" style="text-align: left; padding: 2.5rem; border-left: 4px solid #b71c1c; background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <div class="nsg-icon" style="font-size: 2rem; margin-bottom: 0.75rem;">👁️</div>
            <h3 class="nsg-heading-serif-sm" style="font-size: 1.5rem; margin-bottom: 0.75rem;"><?= __t('Our Vision', 'আমাদের ভিশন') ?></h3>
            <p style="color: <?= e($aboutVisionColor) ?>; font-size: 1.05rem; line-height: 1.6; margin: 0;"><?= nl2br(e(get_localized(['vision' => $aboutVision], 'vision'))) ?></p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/app/footer.php'; ?>
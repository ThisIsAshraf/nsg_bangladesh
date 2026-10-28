<?php

declare(strict_types=1);

if (!defined('BOOTSTRAP_LOADED')) {
    require_once __DIR__ . '/bootstrap.php';
}

$currentLocale = current_locale();
$brand         = setting('site_name', 'Noor Staffing Group');
$logo          = setting('site_logo', '');
$logoWidth     = setting('logo_width', '180');
$nav           = [];

try {
    $q = db()->query("SELECT label, label_bn, url FROM navigation_items WHERE is_active=1 ORDER BY sort_order, id");
    $nav = $q->fetchAll();
} catch (Throwable $t) {
}

if (empty($nav)) {
    $nav = [
        ['label' => 'Home', 'label_bn' => 'হোম', 'url' => 'index.php'],
        ['label' => 'About Us', 'label_bn' => 'আমাদের সম্পর্কে', 'url' => 'about.php'],
        ['label' => 'Careers', 'label_bn' => 'ক্যারিয়ার', 'url' => 'careers.php'],
        ['label' => 'People & Culture', 'label_bn' => 'মানুষ ও সংস্কৃতি', 'url' => 'people.php'],
        ['label' => 'Leadership', 'label_bn' => 'নেতৃত্ব', 'url' => 'board.php'],
        ['label' => 'Contact', 'label_bn' => 'যোগাযোগ', 'url' => 'contact.php'],
    ];
}

$pageTitle = $pageTitle ?? $brand;
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');

function get_lang_url(string $lang): string
{
    $queryParams = $_GET;
    $queryParams['lang'] = $lang;
    return '?' . http_build_query($queryParams);
}

// Ensure logo path has a clean leading slash for local server testing
$logoSrc = '';
if (!empty($logo)) {
    if (strpos($logo, 'http') === 0) {
        $logoSrc = $logo;
    } else {
        $cleanLogo = ltrim($logo, '/');
        $logoSrc = site_url($cleanLogo);
    }
}
?>
<!doctype html>
<html lang="<?= e($currentLocale) ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e(setting('seo_description', 'Connecting people with opportunity.')) ?>">
    <title><?= e($pageTitle) ?> | <?= e($brand) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= e(site_url('assets/style.css')) ?>">
    <?= render_dynamic_css() ?>
</head>

<body class="nsg-body">

    <header class="nsg-site-header">
        <div class="nsg-header-inner">
            <!-- Brand Logo -->
            <a class="brand nsg-brand" href="<?= e(site_url('index.php')) ?>">
                <?php
                $logoPath = setting('site_logo', '');
                $logoW = setting('logo_width', '180');
                ?>
                <?php if (!empty($logoPath)): ?>
                    <img src="<?= e(site_url($logoPath)) ?>"
                        alt="<?= e(setting('site_name', 'Noor Staffing Group')) ?>"
                        style="width: <?= e($logoW) ?>px; max-width: 100%; height: auto; display: block;">
                <?php else: ?>
                    <span class="nsg-logo-text">NOOR <span>STAFFING GROUP</span></span>
                <?php endif; ?>
            </a>

            <!-- Navigation Links -->
            <div class="nsg-header-right">
                <nav class="nsg-main-nav">
                    <?php foreach ($nav as $item): ?>
                        <?php
                        $label = ($currentLocale === 'bn' && !empty($item['label_bn'])) ? $item['label_bn'] : $item['label'];
                        $itemUrl = trim($item['url'], '/');

                        $isActive = ($currentPage === $itemUrl)
                            || ($itemUrl === 'careers.php' && in_array($currentPage, ['job.php', 'apply.php'], true))
                            || ($itemUrl === 'index.php' && $currentPage === '');
                        ?>
                        <a href="<?= e(site_url($item['url'])) ?>" class="<?= $isActive ? 'active' : '' ?>">
                            <?= e($label) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <!-- Language Switcher & Social Links -->
                <div class="nsg-header-utility">
                    <div class="language-switcher nsg-lang-switcher">
                        <a href="<?= e(get_lang_url('en')) ?>" class="<?= $currentLocale === 'en' ? 'active' : '' ?>">ENGLISH</a>
                        <span class="divider">|</span>
                        <a href="<?= e(get_lang_url('bn')) ?>" class="<?= $currentLocale === 'bn' ? 'active' : '' ?>">বাংলা</a>
                    </div>

                    <div class="nsg-social-mini">
                        <a href="<?= e(setting('linkedin_url', '#')) ?>" target="_blank" title="LinkedIn">in</a>
                        <a href="<?= e(setting('facebook_url', '#')) ?>" target="_blank" title="Facebook">f</a>
                        <a href="<?= e(setting('instagram_url', '#')) ?>" target="_blank" title="Instagram">📷</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
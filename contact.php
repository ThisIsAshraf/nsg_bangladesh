<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$pageTitle = __t('Contact Us', 'যোগাযোগ');

// 1. Dynamic Page Copy & Styling Parameters
$contactTitle      = page_section('contact', 'contact_hero_title', 'Get In Touch');
$contactTitleColor = page_section('contact', 'contact_hero_title_color', '#111111');
$contactTitleSize  = page_section('contact', 'contact_hero_title_size', '3rem');

$contactIntro      = page_section('contact', 'contact_intro', 'Reach out for business inquiries, staffing partnerships, or general questions.');
$contactIntroColor = page_section('contact', 'contact_intro_color', '#555555');
$contactIntroSize  = page_section('contact', 'contact_intro_size', '1.15rem');

$successMessage    = page_section('contact', 'contact_success_msg', 'Thank you! Your message has been sent successfully. We will get back to you shortly.');

// 2. Dynamic Office Contact Info & Map Embed
$contactEmail   = setting('contact_email', 'contact@noorstaffing.com');
$contactPhone   = setting('contact_phone', '+880 1700-000000');
$contactAddress = setting(current_locale() === 'bn' ? 'contact_address_bn' : 'contact_address', setting('contact_address', 'Dhaka, Bangladesh'));

$headOfficeAddress = setting(current_locale() === 'bn' ? 'head_office_address_bn' : 'head_office_address', 'New York City, NY, USA');
$mapEmbedUrl       = setting('contact_map_embed_url', '');

$errors = [];
$submittedSuccess = false;

// 3. POST HANDLER: Save Message to DB
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $senderName    = trim($_POST['name'] ?? '');
    $senderEmail   = trim($_POST['email'] ?? '');
    $senderSubject = trim($_POST['subject'] ?? 'General Inquiry');
    $senderMessage = trim($_POST['message'] ?? '');

    // Form Validation
    if ($senderName === '') {
        $errors[] = __t('Your name is required.', 'আপনার নাম দেওয়া আবশ্যক।');
    }
    if ($senderEmail === '' || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = __t('Please enter a valid email address.', 'একটি সঠিক ইমেইল এড্রেস প্রবেশ করান।');
    }
    if ($senderMessage === '') {
        $errors[] = __t('Please enter your message.', 'আপনার বক্তব্য লিখুন।');
    }

    if (empty($errors)) {
        try {
            $stmt = db()->prepare("
                INSERT INTO contact_messages (name, email, subject, message, status, created_at) 
                VALUES (?, ?, ?, ?, 'unread', NOW())
            ");
            $stmt->execute([
                $senderName,
                $senderEmail,
                $senderSubject,
                $senderMessage
            ]);

            log_activity('submitted_contact_message', 'contact_messages', (int)db()->lastInsertId(), ['email' => $senderEmail]);
            $submittedSuccess = true;
        } catch (Throwable $t) {
            $errors[] = __t('Failed to send message. Please try again later.', 'বার্তা পাঠাতে সাময়িক সমস্যা হয়েছে। পরবর্তীতে চেষ্টা করুন।');
        }
    }
}

require __DIR__ . '/app/header.php';
?>

<!-- 1. Dynamic Hero Section -->
<section class="nsg-section nsg-bg-white text-center" style="padding-top: 4.5rem; padding-bottom: 3rem; border-bottom: 1px solid #f0f0f0;">
    <div class="nsg-container">
        <span class="nsg-eyebrow"><?= __t('CONNECT WITH US', 'আমাদের সাথে সংযোগ করুন') ?></span>
        <h1 class="nsg-heading-serif" style="font-size: <?= e($contactTitleSize) ?>; color: <?= e($contactTitleColor) ?>; margin-top: 0.5rem; margin-bottom: 1rem;">
            <?= e(get_localized(['title' => $contactTitle], 'title')) ?>
        </h1>
        <p style="margin: 0 auto; font-size: <?= e($contactIntroSize) ?>; color: <?= e($contactIntroColor) ?>; max-width: 740px; line-height: 1.7;">
            <?= e(get_localized(['intro' => $contactIntro], 'intro')) ?>
        </p>
    </div>
</section>

<!-- 2. Interactive Form & Manageable Office Locations -->
<section class="nsg-section nsg-bg-light" style="padding: 4.5rem 0;">
    <div class="nsg-container nsg-grid-2" style="align-items: flex-start; gap: 3rem;">

        <!-- Left Column: Manageable Contact Form -->
        <div style="background: #fff; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); border: 1px solid #eef2f5;">
            <h2 class="nsg-heading-serif-sm" style="margin-bottom: 1.5rem; font-size: 1.75rem; color: #111;"><?= __t('Send Us a Message', 'আমাদের বার্তা পাঠান') ?></h2>

            <?php if ($submittedSuccess): ?>
                <div style="background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 1.75rem; border-radius: 6px; text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎉</div>
                    <h3 style="margin-top: 0; font-family: Playfair Display, serif;"><?= __t('Message Received!', 'বার্তা জমা হয়েছে!') ?></h3>
                    <p style="margin-bottom: 0; line-height: 1.6;"><?= e(get_localized(['msg' => $successMessage], 'msg')) ?></p>
                </div>
            <?php else: ?>

                <?php if (!empty($errors)): ?>
                    <div style="background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 1rem 1.25rem; border-radius: 6px; margin-bottom: 1.5rem;">
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <?= csrf_field() ?>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="nsg-filter-label" for="name" style="display:block; font-size: 0.8rem; font-weight:700; text-transform:uppercase; margin-bottom: 0.4rem; color:#444;"><?= __t('Your Name *', 'আপনার নাম *') ?></label>
                        <input type="text" name="name" id="name" class="nsg-filter-input" value="<?= e($_POST['name'] ?? '') ?>" placeholder="e.g. Sarah Ahmed" required style="width:100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="nsg-filter-label" for="email" style="display:block; font-size: 0.8rem; font-weight:700; text-transform:uppercase; margin-bottom: 0.4rem; color:#444;"><?= __t('Email Address *', 'ইমেইল এড্রেস *') ?></label>
                        <input type="email" name="email" id="email" class="nsg-filter-input" value="<?= e($_POST['email'] ?? '') ?>" placeholder="e.g. sarah@example.com" required style="width:100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="nsg-filter-label" for="subject" style="display:block; font-size: 0.8rem; font-weight:700; text-transform:uppercase; margin-bottom: 0.4rem; color:#444;"><?= __t('Subject', 'বিষয়') ?></label>
                        <input type="text" name="subject" id="subject" class="nsg-filter-input" value="<?= e($_POST['subject'] ?? '') ?>" placeholder="e.g. Staffing Partnership Inquiry" style="width:100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.75rem;">
                        <label class="nsg-filter-label" for="message" style="display:block; font-size: 0.8rem; font-weight:700; text-transform:uppercase; margin-bottom: 0.4rem; color:#444;"><?= __t('Message *', 'আপনার বার্তা *') ?></label>
                        <textarea name="message" id="message" rows="5" class="nsg-filter-input" style="width:100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px; min-height: 120px;" placeholder="<?= __t('How can we assist you today?', 'আমরা কীভাবে আপনাকে সাহায্য করতে পারি?') ?>" required><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="nsg-btn-red" style="width: 100%; border: none; padding: 0.95rem; font-size: 0.95rem; border-radius: 4px; background: #b71c1c; color: #fff; font-weight: 700; cursor: pointer;"><?= __t('SEND MESSAGE', 'বার্তা পাঠান') ?></button>
                </form>

            <?php endif; ?>
        </div>

        <!-- Right Column: Admin-Managed Locations & Map -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">

            <!-- Local Bangladesh Office Details -->
            <div style="background: #fff; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); border: 1px solid #eef2f5;">
                <span class="nsg-eyebrow" style="color: #b71c1c; font-size: 0.75rem; font-weight:700; display:block; margin-bottom: 0.25rem;"><?= __t('BANGLADESH OPERATIONS HUB', 'বাংলাদেশ অফিস') ?></span>
                <h3 class="nsg-heading-serif-sm" style="font-size: 1.5rem; margin-bottom: 1.25rem; color: #111;"><?= __t('Local Operations Hub', 'আঞ্চলিক অফিস') ?></h3>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <p style="margin: 0; color: #444; line-height: 1.6;">📍 <strong><?= __t('Address', 'ঠিকানা') ?>:</strong> <?= e($contactAddress) ?></p>
                    <p style="margin: 0; color: #444;">📧 <strong>Email:</strong> <a href="mailto:<?= e($contactEmail) ?>" style="color: #b71c1c; font-weight: 600; text-decoration: none;"><?= e($contactEmail) ?></a></p>
                    <p style="margin: 0; color: #444;">📞 <strong>Phone:</strong> <?= e($contactPhone) ?></p>
                </div>
            </div>

            <!-- Head Office Location Box -->
            <div style="background: #000; color: #fff; padding: 2.25rem; border-radius: 8px;">
                <span class="nsg-eyebrow" style="color: #b71c1c; font-size: 0.75rem; font-weight:700;"><?= __t('GLOBAL HEADQUARTERS', 'গ্লোবাল হেডকোয়ার্টার') ?></span>
                <h3 class="nsg-heading-serif-sm" style="color: #fff; font-size: 1.5rem; margin: 0.5rem 0 1rem;"><?= __t('Noor, Inc. US Corporate Office', 'যুক্তরাষ্ট্র হেডকোয়ার্টার') ?></h3>
                <p style="color: #ccc; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">
                    📍 <strong><?= __t('Location', 'অবস্থান') ?>:</strong> <?= e($headOfficeAddress) ?>
                </p>
                <p style="color: #aaa; font-size: 0.85rem; line-height: 1.5; margin: 0;">
                    <?= __t('Headquartered in New York City, powering US workforce solutions across 46 states.', 'নিউ ইয়র্ক ভিত্তিক সদর দপ্তর থেকে পরিচালিত মার্কিন যুক্তরাষ্ট্রব্যাপী সেবাসমূহ।') ?>
                </p>
            </div>

            <!-- Dynamic Google Maps Embed (If URL set in settings) -->
            <?php if (!empty($mapEmbedUrl)): ?>
                <div style="background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #eef2f5; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                    <iframe src="<?= e($mapEmbedUrl) ?>" width="100%" height="240" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            <?php endif; ?>

        </div>

    </div>
</section>

<?php require __DIR__ . '/app/footer.php'; ?>
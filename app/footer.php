<?php

declare(strict_types=1);

$currentLocale = current_locale();
$siteName       = setting('site_name', 'Noor Staffing Group');
$footerText     = setting($currentLocale === 'bn' ? 'footer_text_bn' : 'footer_text', setting('footer_text', 'Connecting talent, teams, and opportunity across Bangladesh and globally.'));
$contactEmail   = setting('contact_email', '');
$contactPhone   = setting('contact_phone', '');
$contactAddress = setting($currentLocale === 'bn' ? 'contact_address_bn' : 'contact_address', setting('contact_address', 'Dhaka, Bangladesh'));
?>
</main>

<!-- Modern Dark Footer matching reference layout -->
<footer class="nsg-main-footer">
    <div class="nsg-container nsg-footer-grid">
        <!-- Social & Brand Links Column -->
        <div class="nsg-footer-col">
            <div class="nsg-footer-social">
                <a href="<?= e(setting('instagram_url', '#')) ?>" target="_blank">Instagram</a>
                <a href="<?= e(setting('linkedin_url', '#')) ?>" target="_blank">LinkedIn</a>
                <a href="<?= e(setting('facebook_url', '#')) ?>" target="_blank">Facebook</a>
            </div>

            <h2 class="nsg-footer-brand-title"><?= e($siteName) ?></h2>
            <p class="nsg-footer-desc"><?= e($footerText) ?></p>

            <?php if ($contactAddress || $contactPhone || $contactEmail): ?>
                <div class="nsg-footer-contact-info">
                    <?php if ($contactAddress): ?><p>📍 <?= e($contactAddress) ?></p><?php endif; ?>
                    <?php if ($contactPhone): ?><p>📞 <?= e($contactPhone) ?></p><?php endif; ?>
                    <?php if ($contactEmail): ?><p>📧 <?= e($contactEmail) ?></p><?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Talent Network Newsletter Widget -->
        <div class="nsg-footer-col">
            <div class="nsg-talent-card">
                <h3><?= __t('Join our talent network and never miss an opening', 'আমাদের ট্যালেন্ট নেটওয়ার্কে যুক্ত থাকুন') ?></h3>
                <form method="post" action="<?= e(site_url('contact.php')) ?>" class="nsg-subscribe-form">
                    <?= csrf_field() ?>
                    <input type="email" name="email" placeholder="<?= __t('Email Address', 'ইমেইল এড্রেস') ?>" required>
                    <button type="submit" class="nsg-btn-join"><?= __t('JOIN', 'যুক্ত হন') ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="nsg-container nsg-copyright-bar">
        <small>© <?= date('Y') ?> <?= e($siteName) ?>. <?= __t('All rights reserved.', 'সর্বস্বত্ব সংরক্ষিত।') ?></small>
    </div>
</footer>

</body>

</html>
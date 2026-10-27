<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$jobId = (int)($_GET['job_id'] ?? 0);
$selectedJob = null;

// Fetch selected job details if job_id provided
if ($jobId > 0) {
    try {
        $stmt = db()->prepare("SELECT * FROM jobs WHERE id = ? AND status = 'published' LIMIT 1");
        $stmt->execute([$jobId]);
        $selectedJob = $stmt->fetch();
    } catch (Throwable $t) {
        $selectedJob = null;
    }
}

// Fetch all active open positions for the dropdown selection
$openJobs = [];
try {
    $stmt = db()->query("SELECT id, title, department, location FROM jobs WHERE status = 'published' AND (closing_date IS NULL OR closing_date >= CURDATE()) ORDER BY title ASC");
    $openJobs = $stmt->fetchAll();
} catch (Throwable $t) {
    $openJobs = [];
}

// Dynamic Page Copy Overrides from admin panel (manage_portal_x92/content/pages.php?page=apply)
$applyTitle   = page_section('apply', 'apply_hero_title', 'Submit Your Application');
$applyIntro   = page_section('apply', 'apply_intro', 'Please fill out your personal details, submit a cover letter, and upload your latest CV/Resume.');
$successMsg   = page_section('apply', 'apply_success_msg', 'Thank you for applying! Our talent acquisition team will review your application.');

$errors = [];
$submittedSuccess = false;

// --- POST HANDLER: Process Application & File Upload ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $submittedJobId = (int)($_POST['job_id'] ?? 0);
    $fullName       = trim($_POST['full_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $coverLetter    = trim($_POST['cover_letter'] ?? '');

    // Form Validations
    if ($submittedJobId <= 0) {
        $errors[] = __t('Please select a position to apply for.', 'অনুগ্রহ করে আবেদনের জন্য একটি পদ নির্বাচন করুন।');
    }
    if ($fullName === '') {
        $errors[] = __t('Full Name is required.', 'আপনার নাম দেওয়া আবশ্যক।');
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = __t('Please enter a valid email address.', 'একটি সঠিক ইমেইল এড্রেস প্রবেশ করান।');
    }
    if ($phone === '') {
        $errors[] = __t('Phone number is required.', 'আপনার ফোন নম্বর দেওয়া আবশ্যক।');
    }

    // Handle CV Document Upload
    $cvPath = null;
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['cv_file']['tmp_name'];
        $fileName = basename($_FILES['cv_file']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExts = ['pdf', 'doc', 'docx'];
        if (!in_array($ext, $allowedExts, true)) {
            $errors[] = __t('Invalid CV file format. Allowed formats: PDF, DOC, DOCX.', 'অকার্যকর ফাইল ফরম্যাট। শুধুমাত্র PDF, DOC, DOCX গ্রহণযোগ্য।');
        } elseif ($_FILES['cv_file']['size'] > 10 * 1024 * 1024) { // 10MB limit
            $errors[] = __t('File size exceeds the 10MB limit.', 'ফাইল সাইজ ১০ মেগাবাইটের বেশি হওয়া যাবে না।');
        } else {
            $uploadDir = __DIR__ . '/uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newFileName = 'cv_' . uniqid('', true) . '.' . $ext;
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $cvPath = 'uploads/documents/' . $newFileName;
            } else {
                $errors[] = __t('Failed to save uploaded CV file.', 'সিভি ফাইল সংরক্ষণ করতে সমস্যা হয়েছে।');
            }
        }
    } else {
        $errors[] = __t('Please upload your CV/Resume document.', 'অনুগ্রহ করে আপনার সিভি/রিজিউমি ফাইল সংযুক্ত করুন।');
    }

    // If no errors, record application into DB
    if (empty($errors)) {
        try {
            $stmt = db()->prepare("
                INSERT INTO job_applications (job_id, full_name, email, phone, cover_letter, cv_path, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'received', NOW(), NOW())
            ");
            $stmt->execute([
                $submittedJobId,
                $fullName,
                $email,
                $phone,
                $coverLetter,
                $cvPath
            ]);

            $submittedSuccess = true;
        } catch (Throwable $t) {
            $errors[] = __t('Database error submitting application. Please try again.', 'আবেদন জমা দিতে সাময়িক সমস্যা হয়েছে। পুনরায় চেষ্টা করুন।');
        }
    }
}

$pageTitle = __t('Apply for Position', 'চাকরির আবেদন');
require __DIR__ . '/app/header.php';
?>

<!-- Application Hero Banner -->
<section class="nsg-section nsg-bg-white text-center" style="padding-top: 4.5rem; padding-bottom: 2.5rem; border-bottom: 1px solid #f0f0f0;">
    <div class="nsg-container">
        <span class="nsg-eyebrow"><?= __t('CAREER OPPORTUNITIES', 'ক্যারিয়ারের সুযোগ') ?></span>
        <h1 class="nsg-heading-serif" style="font-size: 2.75rem; margin-top: 0.5rem; margin-bottom: 1rem;"><?= e(get_localized(['title' => $applyTitle], 'title')) ?></h1>
        <p class="nsg-footer-desc" style="margin: 0 auto; font-size: 1.1rem; max-width: 700px; color: #555;">
            <?= e(get_localized(['intro' => $applyIntro], 'intro')) ?>
        </p>
    </div>
</section>

<!-- Application Form Section -->
<section class="nsg-section nsg-bg-light">
    <div class="nsg-container" style="max-width: 800px; margin: 0 auto;">

        <?php if ($submittedSuccess): ?>
            <div class="nsg-expertise-card text-center" style="padding: 3.5rem 2rem; border-left: 5px solid #2e7d32; background: #fff;">
                <div style="font-size: 3.5rem; margin-bottom: 1rem;">✅</div>
                <h2 class="nsg-heading-serif-sm" style="color: #2e7d32; margin-bottom: 1rem;"><?= __t('Application Received!', 'আবেদন সফলভাবে গৃহীত হয়েছে!') ?></h2>
                <p style="font-size: 1.1rem; color: #555; line-height: 1.7; margin-bottom: 2rem;"><?= e(get_localized(['msg' => $successMsg], 'msg')) ?></p>
                <a href="<?= e(site_url('careers.php')) ?>" class="nsg-btn-red"><?= __t('BROWSE MORE JOBS', 'অন্যান্য চাকরি দেখুন') ?></a>
            </div>
        <?php else: ?>

            <form method="post" action="" enctype="multipart/form-data" class="nsg-job-filter-form" style="padding: 2.5rem;">
                <?= csrf_field() ?>

                <?php if (!empty($errors)): ?>
                    <div style="background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 1rem 1.25rem; border-radius: 6px; margin-bottom: 1.75rem;">
                        <strong style="display: block; margin-bottom: 0.5rem;"><?= __t('Please fix the following errors:', 'অনুগ্রহ করে ত্রুটিগুলো সংশোধন করুন:') ?></strong>
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Target Job Selection -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="nsg-filter-label" for="job_id"><?= __t('Applying For Position *', 'যে পদের জন্য আবেদন করছেন *') ?></label>
                    <select name="job_id" id="job_id" class="nsg-filter-select" required>
                        <option value=""><?= __t('-- Select Position --', '-- পদ নির্বাচন করুন --') ?></option>
                        <?php foreach ($openJobs as $job): ?>
                            <option value="<?= $job['id'] ?>" <?= ($selectedJob && (int)$selectedJob['id'] === (int)$job['id']) || (isset($_POST['job_id']) && (int)$_POST['job_id'] === (int)$job['id']) ? 'selected' : '' ?>>
                                <?= e($job['title']) ?> <?= !empty($job['department']) ? ' (' . e($job['department']) . ')' : '' ?> - <?= e($job['location']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Personal Information -->
                <div class="nsg-grid-2" style="gap: 1.25rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="nsg-filter-label" for="full_name"><?= __t('Full Name *', 'পূর্ণ নাম *') ?></label>
                        <input type="text" name="full_name" id="full_name" class="nsg-filter-input" value="<?= e($_POST['full_name'] ?? '') ?>" placeholder="e.g. John Doe" required>
                    </div>

                    <div class="form-group">
                        <label class="nsg-filter-label" for="email"><?= __t('Email Address *', 'ইমেইল এড্রেস *') ?></label>
                        <input type="email" name="email" id="email" class="nsg-filter-input" value="<?= e($_POST['email'] ?? '') ?>" placeholder="e.g. john@example.com" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="nsg-filter-label" for="phone"><?= __t('Phone Number *', 'ফোন নম্বর *') ?></label>
                    <input type="text" name="phone" id="phone" class="nsg-filter-input" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="e.g. +880 1700-000000" required>
                </div>

                <!-- Cover Letter -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="nsg-filter-label" for="cover_letter"><?= __t('Cover Letter / Message (Optional)', 'কভার লেটার / সংক্ষিপ্ত বক্তব্য') ?></label>
                    <textarea name="cover_letter" id="cover_letter" rows="4" class="nsg-filter-input" style="min-height: 120px;" placeholder="<?= __t('Briefly introduce yourself and outline why you are a good fit for this role...', 'আপনার অভিজ্ঞতা এবং প্রাসঙ্গিক দক্ষতার সংক্ষিপ্ত বিবরণ লিখুন...') ?>"><?= e($_POST['cover_letter'] ?? '') ?></textarea>
                </div>

                <!-- Resume Upload -->
                <div class="form-group" style="margin-bottom: 2rem; background: #fafafa; border: 2px dashed #ddd; padding: 1.5rem; border-radius: 6px; text-align: center;">
                    <label class="nsg-filter-label" for="cv_file" style="margin-bottom: 0.5rem;"><?= __t('Upload CV / Resume Document *', 'সিভি / রিজিউমি ফাইল আপলোড করুন *') ?></label>
                    <p style="font-size: 0.85rem; color: #666; margin-top: 0; margin-bottom: 1rem;"><?= __t('Accepted file formats: PDF, DOC, DOCX (Max size: 10MB)', 'অনুমোদিত ফাইল টাইপ: PDF, DOC, DOCX (সর্বোচ্চ ১০ মেগাবাইট)') ?></p>
                    <input type="file" name="cv_file" id="cv_file" accept=".pdf,.doc,.docx" required style="border: none; padding: 0;">
                </div>

                <button type="submit" class="nsg-btn-red" style="width: 100%; padding: 1rem; font-size: 1rem; border: none; border-radius: 4px;"><?= __t('SUBMIT APPLICATION', 'আবেদন জমা দিন') ?></button>
            </form>

        <?php endif; ?>

    </div>
</section>

<?php require __DIR__ . '/app/footer.php'; ?>
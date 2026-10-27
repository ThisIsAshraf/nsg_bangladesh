# Noor Staffing Bangladesh — Core PHP CMS starter

This is a **working starter foundation**, not a production-complete CMS yet. It includes public corporate pages, job browsing and application intake, contact form, a first-pass admin sign-in, basic page/job/settings management, a normalized MySQL schema, and starter JSON API endpoints.

## Requirements
- PHP 8.1+ with PDO MySQL and Fileinfo
- MySQL 8+ or a compatible MariaDB version with JSON support
- Apache with mod_rewrite (or adapt rewrite rules for your web server)
- HTTPS in production

## Local installation (XAMPP)
1. Copy the `noorstaffing_cms` folder into `C:\xampp\htdocs\`.
2. Create a database by importing `DBSchema/noorstaffing.sql` in phpMyAdmin. The script creates and selects `noorstaffing`.
3. Copy `.env.example` to `.env`; set DB credentials and `APP_URL=http://localhost/noorstaffing_cms`.
4. From this folder run `composer dump-autoload` if Composer is installed. The project can run without third-party Composer packages.
5. Ensure `uploads/images`, `uploads/documents`, and `storage` are writable by PHP. CVs must not be publicly downloadable.
6. Create the first administrator using a one-time PHP script run locally, then delete the script. Example below.
7. Open `http://localhost/noorstaffing_cms/` and sign in at `http://localhost/noorstaffing_cms/manage_portal_x92/auth/sign-in.php`.

## Create the first admin safely
Create a temporary file named `create_admin_once.php` in the project root with the following contents, run it once locally, then **delete it immediately**:

```php
<?php
require __DIR__ . '/app/bootstrap.php';
$email = 'your-admin-email@example.com';
$name = 'Site Administrator';
$password = 'REPLACE-WITH-A-LONG-UNIQUE-PASSWORD';
$roleId = (int) db()->query("SELECT id FROM admin_roles WHERE role_key='super_admin'")->fetchColumn();
$stmt = db()->prepare('INSERT INTO admin_users(role_id,full_name,email,password_hash,status) VALUES(?,?,?,?,?)');
$stmt->execute([$roleId, $name, $email, password_hash($password, PASSWORD_DEFAULT), 'active']);
echo 'Admin created. Delete this script now.';
```
Do not deploy this setup script to a public production server. If the account already exists, the unique email constraint will reject it.

## Routes and API
- Public pages: `/index.php`, `/about.php`, `/careers.php`, `/job.php?slug=...`, `/apply.php?job=...`, `/people.php`, `/board.php`, `/contact.php`
- Admin area (changeable path): `/manage_portal_x92/`
- JSON endpoints: `/api/v1/public/site.php`, `/api/v1/jobs/list.php`, `/api/v1/jobs/detail.php?slug=...`, `/api/v1/contact/create.php`

## Important security / production work still required
- A hidden admin directory is only obscurity, not an access-control mechanism. Keep authentication, authorization, CSRF, throttling, audit logs, and least-privilege database credentials.
- Configure HTTPS and secure cookie settings. Test the `__Host-` cookie name on HTTPS; for local HTTP development you may prefer a separate local session name.
- Add rate limiting and spam/bot controls to public forms and APIs.
- Add a private CV download controller that verifies recruiter permissions. Do not expose `uploads/documents` directly.
- Configure server-level denial for `.env`, SQL files, logs, backups, and private uploads. On production, store CVs outside the document root where possible.
- Add server-side HTML sanitization or a safe rich-text editor before allowing any admin to publish HTML.
- Implement password reset, MFA, account lockout, permission checks per module, activity-log writes, API authentication where needed, and request validation.
- Add full CRUD and media upload flows for pages, theme blocks, board members, employee memories, Employee of the Month, and stories.
- Add automated tests, backups, retention policy, privacy notice/consent text reviewed for the company, and accessibility checks.

## Architectural notes
- Relational tables separate admins/roles, pages, navigation, settings, media, jobs, applications, employee stories, recognition, memories, board members, messages, and activity logs.
- Public data is read from MySQL. Site name, announcement, theme colors, core typography, and many homepage text values can be changed in the admin settings screen.
- API endpoints return JSON and use prepared PDO statements. Keep API versioning under `/api/v1/` and introduce authentication scopes for any future write endpoints.

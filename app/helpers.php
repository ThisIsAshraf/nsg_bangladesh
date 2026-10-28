<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(?string $str): string
    {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Generate full URL or root-relative path correctly
 */
if (!function_exists('site_url')) {
    function site_url(string $path = ''): string
    {
        $appUrl = rtrim(getenv('APP_URL') ?: '', '/');
        $cleanPath = '/' . ltrim($path, '/');

        if ($appUrl !== '') {
            return $appUrl . $cleanPath;
        }

        // Auto-detect project subdirectory under localhost (e.g. /nsg/)
        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
        $projDir = rtrim(dirname(__DIR__), '/\\'); // root project folder

        $basePath = '';
        if ($docRoot !== '' && str_starts_with($projDir, $docRoot)) {
            $basePath = str_replace('\\', '/', substr($projDir, strlen($docRoot)));
        }

        return rtrim($basePath, '/') . $cleanPath;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            exit('CSRF token validation failed.');
        }
    }
}

/**
 * Get setting value from database with optional fallback
 */
if (!function_exists('setting')) {
    function setting(string $key, string $default = ''): string
    {
        static $settingsCache = null;

        if ($settingsCache === null) {
            $settingsCache = [];
            try {
                $stmt = db()->query('SELECT `key`, `value` FROM settings');
                while ($row = $stmt->fetch()) {
                    $settingsCache[$row['key']] = (string)$row['value'];
                }
            } catch (Throwable $t) {
                // Return default on error
            }
        }

        return $settingsCache[$key] ?? $default;
    }
}

if (!function_exists('page_section')) {
    function page_section(string $pageSlug, string $sectionKey, string $default = ''): string
    {
        static $cache = [];
        $cacheKey = $pageSlug . ':' . $sectionKey;
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        try {
            $stmt = db()->prepare("SELECT content_value FROM page_sections WHERE page_slug = ? AND section_key = ? LIMIT 1");
            $stmt->execute([$pageSlug, $sectionKey]);
            $val = $stmt->fetchColumn();
            $cache[$cacheKey] = ($val !== false && $val !== null && $val !== '') ? (string)$val : $default;
        } catch (Throwable $t) {
            $cache[$cacheKey] = $default;
        }

        return $cache[$cacheKey];
    }
}

if (!function_exists('current_locale')) {
    function current_locale(): string
    {
        if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'bn'], true)) {
            $_SESSION['locale'] = $_GET['lang'];
        }
        return $_SESSION['locale'] ?? 'en';
    }
}

if (!function_exists('__t')) {
    function __t(string $en, string $bn = ''): string
    {
        return current_locale() === 'bn' && $bn !== '' ? $bn : $en;
    }
}

if (!function_exists('get_localized')) {
    function get_localized(array $data, string $key, string $table = '', int $id = 0): string
    {
        $locale = current_locale();
        if ($locale === 'bn') {
            if (!empty($data[$key . '_bn'])) {
                return (string)$data[$key . '_bn'];
            }
        }
        return (string)($data[$key] ?? '');
    }
}

if (!function_exists('admin_user')) {
    function admin_user(): ?array
    {
        return $_SESSION['admin'] ?? null;
    }
}

if (!function_exists('require_admin')) {
    function require_admin(string $permission = '*'): void
    {
        $admin = admin_user();
        if (!$admin) {
            header('Location: ' . site_url('admin/auth/sign-in.php'));
            exit;
        }
    }
}

if (!function_exists('has_permission')) {
    function has_permission(string $permission): bool
    {
        $admin = admin_user();
        if (!$admin) return false;
        if (($admin['role_key'] ?? '') === 'super_admin') return true;
        return $permission === '*';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text ?: 'n-a');
    }
}

if (!function_exists('log_activity')) {
    function log_activity(string $action, ?string $entityType = null, ?int $entityId = null, array $details = []): void
    {
        try {
            $admin = admin_user();
            $adminId = $admin['id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmt = db()->prepare("INSERT INTO activity_logs (admin_user_id, action, entity_type, entity_id, details_json, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $adminId,
                $action,
                $entityType,
                $entityId,
                !empty($details) ? json_encode($details) : null,
                $ip
            ]);
        } catch (Throwable $t) {
            // Fail silently if activity_logs table isn't migrated
        }
    }
}

if (!function_exists('track_visitor')) {
    function track_visitor(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (str_contains($uri, '/admin') || str_contains($uri, '/manage_portal_x92') || str_contains($uri, '/assets')) {
            return;
        }

        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ipHash = hash('sha256', $ip . 'salt_nsg_analytics');
            $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
            $ref = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 255);

            $stmt = db()->prepare("INSERT INTO analytics_visits (page_url, ip_hash, user_agent, referrer, visited_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$uri, $ipHash, $ua, $ref]);
        } catch (Throwable $t) {
            // Safety fallback
        }
    }
}

if (!function_exists('render_dynamic_css')) {
    function render_dynamic_css(): string
    {
        $primaryColor = setting('theme_primary', '#123B5D');
        $accentColor  = setting('theme_accent', '#E9A23B');
        $bodySize     = setting('body_font_size', '16') . 'px';
        $headingSize  = setting('heading_font_size', '42') . 'px';
        $logoWidth    = setting('logo_width', '180') . 'px';

        return "
        <style id=\"dynamic-theme-vars\">
            :root {
                --primary-color: {$primaryColor};
                --accent-color: {$accentColor};
                --brand: {$primaryColor};
                --accent: {$accentColor};
                --body-size: {$bodySize};
                --heading-size: {$headingSize};
                --logo-width: {$logoWidth};
            }
        </style>";
    }
}

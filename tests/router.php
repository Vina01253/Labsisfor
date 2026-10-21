<?php
// ============================================================================
//  [XAMPP-TWEAK] ROUTER UNTUK PHP BUILT-IN SERVER (KHUSUS TESTING)
// ----------------------------------------------------------------------------
//  PHP built-in server TIDAK membaca .htaccess, jadi URL tanpa ".php"
//  (mis. /login, /admin) tidak otomatis dipetakan ke login.php / admin.php.
//  Router ini meniru aturan .htaccess:
//      RewriteCond %{REQUEST_FILENAME} !-d
//      RewriteCond %{REQUEST_FILENAME}.php -f
//      RewriteRule ^(.*)$ $1.php
//
//  Cara pakai:
//      C:\Xamppp\php\php.exe -S 127.0.0.1:8099 tests\router.php
//
//  CATATAN: file ini HANYA untuk testing. Pada XAMPP/Apache asli,
//  .htaccess sudah menangani rewrite ini.
// ============================================================================

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = __DIR__ . '/../' . ltrim($uri, '/');

// Jika file/dir ada apa adanya, tampilkan langsung.
if ($uri !== '/' && file_exists($path) && !is_dir($path)) {
    return false;
}

// Tiru rewrite ke .php tanpa ekstensi.
if (file_exists($path . '.php')) {
    require $path . '.php';
    return true;
}

// URL tanpa ekstensi di dalam direktori (mis. /admin?page=home).
if (is_dir($path) && file_exists(rtrim($path, '/') . '/index.php')) {
    require rtrim($path, '/') . '/index.php';
    return true;
}

// Root -> index.php
if ($uri === '/') {
    require __DIR__ . '/../index.php';
    return true;
}

// Fallback: 404.
http_response_code(404);
require __DIR__ . '/../404.php';
return true;

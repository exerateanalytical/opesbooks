<?php

/**
 * cPanel public_html entrypoint (FALLBACK approach).
 *
 * Use this ONLY if you cannot change the domain's Document Root in cPanel.
 * Place this file in public_html as "index.php", copy the contents of the
 * project's public/ folder into public_html (manifest.json, sw.js, icon.svg,
 * favicon, robots.txt, .htaccess, build/ if present, storage symlink), and
 * keep the rest of the Laravel app in a sibling folder named "opesbooks".
 *
 * Adjust $base if your app folder has a different name/location.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Path to the Laravel application root (one level above public_html).
$base = __DIR__ . '/../opesbooks';

if (file_exists($maintenance = $base . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $base . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $base . '/bootstrap/app.php';

$app->handleRequest(Request::capture());

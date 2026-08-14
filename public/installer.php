<?php

/**
 * WorkForge Shared Hosting Migration & Setup Runner for InfinityFree
 * Access via: http://workforgemarketplace.gt.tc/installer.php?secret=workforge2026
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
@ini_set('memory_limit', '512M');
@set_time_limit(300);

$secretKey = 'workforge2026';

if (($_GET['secret'] ?? '') !== $secretKey) {
    http_response_code(403);
    die('<!DOCTYPE html><html><head><title>WorkForge Installer</title><style>body{font-family:sans-serif;padding:40px;background:#0f172a;color:#fff;text-align:center;}</style></head><body><h1>403 Unauthorized</h1><p>Please provide the correct setup secret parameter.</p></body></html>');
}

define('LARAVEL_START', microtime(true));

// Strict open_basedir safe paths within current directory
$baseDir = __DIR__;
if (!file_exists($baseDir . '/vendor/autoload.php') && file_exists($baseDir . '/../vendor/autoload.php')) {
    $baseDir = dirname($baseDir);
}

$autoloadPath = $baseDir . '/vendor/autoload.php';
$bootstrapPath = $baseDir . '/bootstrap/app.php';

if (!file_exists($autoloadPath)) {
    die("<h1>Vendor directory not found at {$autoloadPath}!</h1><p>Please run unzip.php first.</p>");
}

require $autoloadPath;
$app = require_once $bootstrapPath;

// Bootstrap Laravel container and configuration
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbHost = config('database.connections.mysql.host') ?? 'sql301.infinityfree.com';
$dbName = config('database.connections.mysql.database') ?? 'if0_42654988_workforge';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkForge — Database Setup</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #e2e8f0; padding: 40px 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        h1 { color: #10b981; margin-top: 0; }
        pre { background: #090d16; border: 1px solid #1e293b; color: #34d399; padding: 16px; border-radius: 8px; overflow-x: auto; font-size: 13px; max-height: 400px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; background: rgba(16, 185, 129, 0.2); color: #34d399; font-weight: bold; font-size: 12px; margin-bottom: 16px; }
        .btn { display: inline-block; background: #10b981; color: #022c22; font-weight: bold; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge">InfinityFree Environment Setup</span>
        <h1>⚡ WorkForge Database Migration</h1>
        <p>Target MySQL Host: <strong><?= htmlspecialchars($dbHost) ?></strong></p>
        <p>Target Database: <strong><?= htmlspecialchars($dbName) ?></strong></p>

        <?php
        try {
            // Run migrations with force flag
            $exitCode = $kernel->call('migrate', ['--force' => true, '--seed' => true]);
            $output = $kernel->output();

            echo "<h3>Migration Console Output:</h3>";
            echo "<pre>" . htmlspecialchars($output ?: "Migrations completed with exit code: {$exitCode}") . "</pre>";

            // Clear cache
            $kernel->call('optimize:clear');
            echo "<p style='color:#10b981; font-weight:bold; font-size:16px;'>✓ Setup completed successfully! All tables, categories, skills, sample jobs, and users are ready.</p>";
            echo "<a href='/' class='btn'>Launch WorkForge Marketplace &rarr;</a>";
        } catch (\Throwable $e) {
            echo "<h3 style='color:#f87171;'>Setup Note:</h3>";
            echo "<pre style='color:#f87171;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
            
            echo "<div style='background:#0f172a; padding:16px; border-radius:8px; margin-top:16px; border:1px solid #334155;'>";
            echo "<p style='color:#fbbf24; font-weight:bold; margin-top:0;'>💡 Quick Fix Checklist:</p>";
            echo "<ol style='color:#cbd5e1; font-size:14px; padding-left:20px; line-height:1.8;'>";
            echo "<li>Make sure you created the database in your InfinityFree cPanel &rarr; <strong>MySQL Databases</strong> (e.g. <code>if0_42654988_workforge</code>).</li>";
            echo "<li>If your database name is different, update <code>DB_DATABASE</code> in your <code>.env</code> file.</li>";
            echo "</ol>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>

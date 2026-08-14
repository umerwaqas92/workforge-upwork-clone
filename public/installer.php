<?php

/**
 * WorkForge Shared Hosting Migration & Setup Runner for InfinityFree
 * Access via: https://yourdomain.infinityfreeapp.com/installer.php?secret=workforge2026
 */

$secretKey = 'workforge2026';

if (($_GET['secret'] ?? '') !== $secretKey) {
    http_response_code(403);
    die('<!DOCTYPE html><html><head><title>WorkForge Installer</title><style>body{font-family:sans-serif;padding:40px;background:#0f172a;color:#fff;text-align:center;}</style></head><body><h1>403 Unauthorized</h1><p>Please provide the correct setup secret parameter.</p></body></html>');
}

define('LARAVEL_START', microtime(true));

// Locate vendor autoload
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    $autoloadPath = __DIR__ . '/vendor/autoload.php';
}

if (!file_exists($autoloadPath)) {
    die("<h1>Vendor directory not found!</h1><p>Ensure vendor folder is uploaded.</p>");
}

require $autoloadPath;

$bootstrapPath = __DIR__ . '/../bootstrap/app.php';
if (!file_exists($bootstrapPath)) {
    $bootstrapPath = __DIR__ . '/bootstrap/app.php';
}

$app = require_once $bootstrapPath;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkForge — InfinityFree Deployment Setup</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #e2e8f0; padding: 40px 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        h1 { color: #10b981; margin-top: 0; }
        pre { background: #090d16; border: 1px solid #1e293b; color: #34d399; padding: 16px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; background: rgba(16, 185, 129, 0.2); color: #34d399; font-weight: bold; font-size: 12px; margin-bottom: 16px; }
        .btn { display: inline-block; background: #10b981; color: #022c22; font-weight: bold; padding: 10px 20px; border-radius: 8px; text-decoration: none; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge">InfinityFree Environment Setup</span>
        <h1>⚡ WorkForge Database Migration</h1>
        <p>Running Laravel database migrations and sample seeders on <strong><?= config('database.connections.mysql.host') ?></strong>...</p>

        <?php
        try {
            // Run migrations with force flag
            $exitCode = $kernel->call('migrate', ['--force' => true, '--seed' => true]);
            $output = $kernel->output();

            echo "<h3>Migration Console Output:</h3>";
            echo "<pre>" . htmlspecialchars($output ?: "Migrations executed with exit code {$exitCode}") . "</pre>";

            // Clear cache
            $kernel->call('optimize:clear');
            echo "<p style='color:#10b981; font-weight:bold;'>✓ Setup completed successfully! All database tables and seeders are initialized.</p>";
            echo "<a href='/' class='btn'>Launch WorkForge Marketplace &rarr;</a>";
        } catch (\Throwable $e) {
            echo "<h3 style='color:#f87171;'>Setup Error:</h3>";
            echo "<pre style='color:#f87171;'>" . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "<p style='color:#94a3b8;'>Tip: Verify that your database name in <code>.env</code> matches your InfinityFree MySQL database (e.g. <code>if0_42654988_workforge</code>) created in the Control Panel.</p>";
        }
        ?>
    </div>
</body>
</html>

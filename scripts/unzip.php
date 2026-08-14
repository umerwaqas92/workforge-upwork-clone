<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@ini_set('memory_limit', '512M');
@set_time_limit(600);

if (($_GET['secret'] ?? '') !== 'workforge2026') {
    die("Access denied. Invalid secret parameter.");
}

$zipFile = __DIR__ . '/workforge_deploy.zip';

if (!file_exists($zipFile)) {
    die("<h1>workforge_deploy.zip not found</h1><p>Ensure workforge_deploy.zip is located in " . htmlspecialchars(__DIR__) . "</p>");
}

echo "<!DOCTYPE html><html><head><title>Unpacking WorkForge</title><style>body{font-family:sans-serif;padding:30px;background:#0f172a;color:#e2e8f0;line-height:1.5;}pre{background:#1e293b;padding:15px;border-radius:8px;color:#34d399;max-height:400px;overflow-y:auto;}.btn{display:inline-block;background:#10b981;color:#022c22;padding:12px 24px;font-weight:bold;border-radius:8px;text-decoration:none;margin-top:20px;}</style></head><body>";
echo "<h1 style='color:#10b981;'>⚡ Extracting WorkForge Package...</h1>";

$zip = new ZipArchive;
$res = $zip->open($zipFile);

if ($res === TRUE) {
    $numFiles = $zip->numFiles;
    echo "<p>Found <strong>{$numFiles}</strong> files in archive. Extracting now...</p>";
    
    // Extract
    $zip->extractTo(__DIR__);
    $zip->close();

    // Ensure permissions on storage
    @chmod(__DIR__ . '/storage', 0777);
    @chmod(__DIR__ . '/bootstrap/cache', 0777);

    echo "<h2 style='color:#34d399;'>✓ All Files Extracted Successfully!</h2>";
    echo "<p>Your application and vendor libraries are ready.</p>";
    echo "<p><a href='/installer.php?secret=workforge2026' class='btn'>Run Database Setup & Migrations &rarr;</a></p>";
} else {
    echo "<h2 style='color:#f87171;'>Failed to open zip archive. Code: {$res}</h2>";
}

echo "</body></html>";

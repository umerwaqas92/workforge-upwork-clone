<?php

/**
 * Automated WorkForge FTP Deployer for InfinityFree
 */

$host = getenv('FTP_HOST') ?: 'ftpupload.net';
$user = getenv('FTP_USER') ?: 'if0_42654988';
$pass = getenv('FTP_PASS') ?: '2UwmsMo2RskP';
$port = (int) (getenv('FTP_PORT') ?: 21);
$remoteRoot = '/htdocs';

echo "====================================================\n";
echo "🚀 WorkForge Automated FTP Deployer to InfinityFree\n";
echo "====================================================\n";
echo "Host:     {$host}:{$port}\n";
echo "User:     {$user}\n";
echo "Target:   {$remoteRoot}\n";
echo "----------------------------------------------------\n";

echo "Connecting to FTP server...";
$ftp = @ftp_connect($host, $port, 30);
if (!$ftp) {
    die("\n❌ Error: Could not connect to {$host}:{$port}\n");
}
echo " Connected!\n";

echo "Authenticating...";
if (!@ftp_login($ftp, $user, $pass)) {
    ftp_close($ftp);
    die("\n❌ Error: FTP Authentication failed for user {$user}\n");
}
echo " Logged in successfully!\n";

ftp_pasv($ftp, true);

// Exclude patterns
$excludes = [
    '.git',
    'node_modules',
    'tests',
    'docs/screenshots',
    'storage/logs/laravel.log',
    'capture-screenshots.js',
    'scripts',
    '.phpunit.result.cache',
    'database/database.sqlite',
    'database/database.sqlite-journal',
];

$sourceDir = realpath(__DIR__ . '/..');

echo "Scanning local files to deploy from {$sourceDir}...\n";

function getFilesToUpload($dir, $excludes, $baseDir) {
    $files = [];
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        $relPath = ltrim(str_replace($baseDir, '', $path), '/');

        // Check exclusions
        $skip = false;
        foreach ($excludes as $exc) {
            if ($relPath === $exc || str_starts_with($relPath, $exc . '/')) {
                $skip = true;
                break;
            }
        }
        if ($skip) continue;

        if (is_dir($path)) {
            $files[] = ['type' => 'dir', 'path' => $relPath, 'full' => $path];
            $files = array_merge($files, getFilesToUpload($path, $excludes, $baseDir));
        } else {
            $files[] = ['type' => 'file', 'path' => $relPath, 'full' => $path];
        }
    }
    return $files;
}

$uploadList = getFilesToUpload($sourceDir, $excludes, $sourceDir);
$total = count($uploadList);

echo "Found {$total} files and directories to deploy.\n";
echo "Starting upload into {$remoteRoot}...\n";

// Helper to make remote directory recursively
function ftp_mksubdirs($ftp, $remotePath) {
    $parts = explode('/', trim($remotePath, '/'));
    $current = '';
    foreach ($parts as $part) {
        $current .= '/' . $part;
        if (!@ftp_chdir($ftp, $current)) {
            @ftp_mkdir($ftp, $current);
            @ftp_chmod($ftp, 0755, $current);
        }
    }
}

$uploadedCount = 0;
$startTime = microtime(true);

foreach ($uploadList as $idx => $item) {
    $remoteItemPath = $remoteRoot . '/' . $item['path'];
    $progress = round((($idx + 1) / $total) * 100);

    if ($item['type'] === 'dir') {
        ftp_mksubdirs($ftp, $remoteItemPath);
    } else {
        $remoteDir = dirname($remoteItemPath);
        ftp_mksubdirs($ftp, $remoteDir);

        $uploadSuccess = @ftp_put($ftp, $remoteItemPath, $item['full'], FTP_BINARY);
        if (!$uploadSuccess) {
            // Retry once
            ftp_pasv($ftp, true);
            $uploadSuccess = @ftp_put($ftp, $remoteItemPath, $item['full'], FTP_BINARY);
        }

        if ($uploadSuccess) {
            $uploadedCount++;
            if ($uploadedCount % 50 === 0 || $uploadedCount === $total) {
                $elapsed = round(microtime(true) - $startTime, 1);
                echo "[{$progress}%] Uploaded {$uploadedCount}/{$total} files... ({$elapsed}s elapsed)\n";
            }
        } else {
            echo "⚠️ Warning: Failed to upload {$item['path']}\n";
        }
    }
}

ftp_close($ftp);

$totalTime = round(microtime(true) - $startTime, 1);
echo "----------------------------------------------------\n";
echo "✅ Deployment completed successfully in {$totalTime}s!\n";
echo "Uploaded {$uploadedCount} files into InfinityFree /htdocs\n";
echo "====================================================\n";

<?php

/**
 * High-Speed Archive Deployer for InfinityFree
 */

$host = 'ftpupload.net';
$user = 'if0_42654988';
$pass = '2UwmsMo2RskP';
$port = 21;
$remoteRoot = '/htdocs';

echo "====================================================\n";
echo "⚡ WorkForge High-Speed Archive FTP Deployer\n";
echo "====================================================\n";
echo "Host:     {$host}:{$port}\n";
echo "User:     {$user}\n";
echo "Target:   {$remoteRoot}\n";
echo "----------------------------------------------------\n";

echo "Connecting to FTP server...";
$ftp = @ftp_connect($host, $port, 60);
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

$zipPath = __DIR__ . '/../workforge_deploy.zip';
$unzipScriptPath = __DIR__ . '/unzip.php';

if (!file_exists($zipPath)) {
    die("❌ Error: workforge_deploy.zip not found!\n");
}

$zipSizeMb = round(filesize($zipPath) / 1024 / 1024, 2);
echo "Uploading workforge_deploy.zip ({$zipSizeMb} MB) to {$remoteRoot}/workforge_deploy.zip...\n";

$startTime = microtime(true);

// Upload ZIP
if (ftp_put($ftp, "{$remoteRoot}/workforge_deploy.zip", $zipPath, FTP_BINARY)) {
    $elapsed = round(microtime(true) - $startTime, 1);
    echo "✓ Archive uploaded in {$elapsed}s!\n";
} else {
    die("❌ Failed to upload zip archive.\n");
}

// Upload extractor
echo "Uploading server-side extractor unzip.php...\n";
if (ftp_put($ftp, "{$remoteRoot}/unzip.php", $unzipScriptPath, FTP_BINARY)) {
    echo "✓ Extractor uploaded!\n";
} else {
    echo "⚠️ Warning: Failed to upload unzip.php\n";
}

ftp_close($ftp);

echo "====================================================\n";
echo "🎉 UPLOAD COMPLETE IN " . round(microtime(true) - $startTime, 1) . "s!\n";
echo "====================================================\n";
echo "To finish deployment, visit this URL in your browser:\n";
echo "👉 http://YOUR_DOMAIN/unzip.php?secret=workforge2026\n";
echo "====================================================\n";

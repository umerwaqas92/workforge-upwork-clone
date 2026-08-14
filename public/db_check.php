<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>WorkForge Hosting Environment Status</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// 1. Check PDO SQLite
$sqliteOk = in_array('sqlite', PDO::getAvailableDrivers());
echo "<p>PDO SQLite Extension: " . ($sqliteOk ? "<strong style='color:green;'>AVAILABLE (Can run 100% standalone instantly without MySQL setup!)</strong>" : "<strong style='color:red;'>NOT AVAILABLE</strong>") . "</p>";

// 2. Check MySQL
$host = 'sql301.infinityfree.com';
$user = 'if0_42654988';
$pass = '2UwmsMo2RskP';

echo "<h3>MySQL Check:</h3>";
try {
    $pdo = new PDO("mysql:host={$host};port=3306", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "<p style='color:green;'>✓ MySQL Connected successfully!</p>";
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Databases found: " . json_encode($databases) . "</p>";
} catch (\Throwable $e) {
    echo "<p style='color:orange;'>MySQL Note: " . htmlspecialchars($e->getMessage()) . "</p>";
}

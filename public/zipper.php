<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
set_time_limit(600); // 10 minutes

// 1. Identify the Root (One level above public)
$rootPath = realpath(__DIR__ . '/../');
$zipFile = __DIR__ . '/FULL_PROJECT_BACKUP.zip';

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Error: Could not create zip file.");
}

echo "Root directory identified as: " . $rootPath . "<br>";
echo "Starting compression...<br><br>";

// 2. Create the iterator
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$count = 0;
foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();

        // Create a relative path so the zip structure looks like the original project
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // EXCLUDE heavy folders to ensure the script doesn't crash
        if (
            strpos($relativePath, 'vendor/') === 0 ||
            strpos($relativePath, 'node_modules/') === 0 ||
            strpos($relativePath, 'public/FULL_PROJECT_BACKUP.zip') !== false
        ) {
            continue;
        }

        $zip->addFile($filePath, $relativePath);
        $count++;
    }
}

$zip->close();

echo "<h3>SUCCESS!</h3>";
echo "Total files zipped: " . $count . "<br>";
echo "<b>Location:</b> Inside your 'public' folder.<br>";
echo "<b>Filename:</b> FULL_PROJECT_BACKUP.zip<br>";
echo "Refresh FileZilla and download now.";

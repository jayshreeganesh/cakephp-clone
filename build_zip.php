<?php
$zipFileName = __DIR__ . '/deployment_build.zip';
if (file_exists($zipFileName)) unlink($zipFileName);

$zip = new ZipArchive();
if ($zip->open($zipFileName, ZipArchive::CREATE) !== TRUE) die("Error: Cannot create zip file.\n");

$excludeDirs = ['node_modules', 'vendor', 'tests', '.git', '.github'];
$excludeExts = ['sqlite', 'zip', 'log'];

$dirIterator = new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($dirIterator);

$fileCount = 0;
foreach ($files as $file) {
    $path = $file->getRealPath();
    $relativePath = str_replace('\\', '/', substr($path, strlen(__DIR__) + 1));
    $skip = false;
    foreach ($excludeDirs as $excluded) {
        if (strpos($relativePath, $excluded . '/') === 0 || $relativePath === $excluded) { $skip = true; break; }
    }
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (in_array($ext, $excludeExts) || basename($path) === 'build_zip.php' || basename($path) === 'seed_ui.php') $skip = true;

    if (!$skip) { $zip->addFile($path, $relativePath); $fileCount++; }
}
$zip->close();

echo "<div style='font-family:sans-serif; text-align:center; padding: 50px;'>";
echo "<h1>Deployment Zip Created!</h1>";
echo "<p>Success! Created <b>deployment_build.zip</b> containing exactly {$fileCount} files.</p>";
echo "<p><a href='/deployment_build.zip' style='display:inline-block; background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Download Zip</a></p>";
echo "<p><a href='/'>Go back</a></p>";
echo "</div>";


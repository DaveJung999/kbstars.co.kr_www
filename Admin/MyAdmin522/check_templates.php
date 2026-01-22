<?php
/**
 * Check if template files exist
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Template Files Check</h1>";

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

$templatesDir = ROOT_PATH . 'templates';
$loginDir = $templatesDir . DIRECTORY_SEPARATOR . 'login';

echo "<h2>1. Directory Check</h2>";
echo "<p>Templates directory: <code>$templatesDir</code></p>";
echo "<p>Exists: " . (file_exists($templatesDir) ? "<span style='color:green'>Yes</span>" : "<span style='color:red'>No</span>") . "</p>";

echo "<p>Login directory: <code>$loginDir</code></p>";
echo "<p>Exists: " . (file_exists($loginDir) ? "<span style='color:green'>Yes</span>" : "<span style='color:red'>No</span>") . "</p>";

echo "<h2>2. Files in login directory</h2>";
if (file_exists($loginDir)) {
    $files = scandir($loginDir);
    $files = array_filter($files, function($file) {
        return $file !== '.' && $file !== '..';
    });
    
    if (empty($files)) {
        echo "<p style='color:red'>✗ No files found in login directory!</p>";
    } else {
        echo "<ul>";
        foreach ($files as $file) {
            $fullPath = $loginDir . DIRECTORY_SEPARATOR . $file;
            $isFile = is_file($fullPath);
            $isDir = is_dir($fullPath);
            echo "<li>";
            echo $file;
            if ($isFile) {
                echo " <span style='color:green'>(file)</span>";
            } elseif ($isDir) {
                echo " <span style='color:blue'>(directory)</span>";
            }
            echo "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color:red'>✗ Login directory does not exist!</p>";
}

echo "<h2>3. Required template files</h2>";
$requiredFiles = [
    'login/header.twig',
    'login/index.twig',
    'login/footer.twig',
];

foreach ($requiredFiles as $file) {
    $fullPath = $templatesDir . DIRECTORY_SEPARATOR . $file;
    $exists = file_exists($fullPath);
    echo "<p>";
    echo $file . ": ";
    if ($exists) {
        echo "<span style='color:green'>✓ Exists</span>";
    } else {
        echo "<span style='color:red'>✗ Missing</span>";
    }
    echo "</p>";
}

echo "<h2>4. All .twig files in templates directory</h2>";
function findTwigFiles($dir, $baseDir = '') {
    $files = [];
    if (!is_dir($dir)) {
        return $files;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $relativePath = $baseDir ? $baseDir . '/' . $item : $item;
        
        if (is_dir($path)) {
            $files = array_merge($files, findTwigFiles($path, $relativePath));
        } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'twig') {
            $files[] = $relativePath;
        }
    }
    
    return $files;
}

$twigFiles = findTwigFiles($templatesDir);
if (empty($twigFiles)) {
    echo "<p style='color:red'>✗ No .twig files found in templates directory!</p>";
    echo "<p><strong>This means template files were not uploaded properly.</strong></p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>Did you upload all files including .twig files?</li>";
    echo "<li>Are .twig files excluded by .gitignore?</li>";
    echo "<li>Did the upload process skip certain file types?</li>";
    echo "</ul>";
} else {
    echo "<p style='color:green'>✓ Found " . count($twigFiles) . " .twig files</p>";
    echo "<details><summary>Show all .twig files (click to expand)</summary>";
    echo "<ul style='max-height:400px;overflow:auto;'>";
    foreach ($twigFiles as $file) {
        echo "<li>$file</li>";
    }
    echo "</ul></details>";
}

echo "<hr>";
echo "<p><a href='index.php'>Try index.php</a></p>";

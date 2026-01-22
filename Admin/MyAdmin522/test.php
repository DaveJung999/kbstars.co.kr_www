<?php
/**
 * Simple test file to check phpMyAdmin requirements
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>phpMyAdmin Test</h1>";

echo "<h2>1. PHP Version</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Required: 7.2.5+<br>";
if (PHP_VERSION_ID >= 70205) {
    echo "<span style='color:green'>✓ OK</span><br>";
} else {
    echo "<span style='color:red'>✗ FAILED</span><br>";
}

echo "<h2>2. Required Extensions</h2>";
$required = ['mysqli', 'mbstring', 'xml', 'json', 'session', 'zip', 'gd'];
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    echo "$ext: " . ($loaded ? "<span style='color:green'>✓</span>" : "<span style='color:red'>✗</span>") . "<br>";
}

echo "<h2>3. Optional but Recommended</h2>";
$optional = ['sodium', 'openssl', 'curl', 'iconv'];
foreach ($optional as $ext) {
    $loaded = extension_loaded($ext);
    echo "$ext: " . ($loaded ? "<span style='color:green'>✓</span>" : "<span style='color:orange'>⚠</span>") . "<br>";
}

echo "<h2>4. File Checks</h2>";
$files = [
    'vendor/autoload.php' => ROOT_PATH . 'vendor/autoload.php',
    'libraries/constants.php' => ROOT_PATH . 'libraries/constants.php',
    'config.inc.php' => ROOT_PATH . 'config.inc.php',
    'tmp directory' => ROOT_PATH . 'tmp',
];

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

foreach ($files as $name => $path) {
    $exists = file_exists($path);
    $readable = is_readable($path);
    echo "$name: " . ($exists ? "<span style='color:green'>✓</span>" : "<span style='color:red'>✗</span>");
    if ($exists && !$readable) {
        echo " <span style='color:orange'>(not readable)</span>";
    }
    echo "<br>";
}

echo "<h2>5. Constants Test</h2>";
try {
    require_once ROOT_PATH . 'libraries/constants.php';
    echo "constants.php loaded: <span style='color:green'>✓</span><br>";
    if (defined('AUTOLOAD_FILE')) {
        echo "AUTOLOAD_FILE: " . AUTOLOAD_FILE . "<br>";
        echo "AUTOLOAD_FILE exists: " . (file_exists(AUTOLOAD_FILE) ? "<span style='color:green'>✓</span>" : "<span style='color:red'>✗</span>") . "<br>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>Error loading constants.php: " . $e->getMessage() . "</span><br>";
}

echo "<h2>6. Autoload Test</h2>";
try {
    if (defined('AUTOLOAD_FILE') && file_exists(AUTOLOAD_FILE)) {
        require AUTOLOAD_FILE;
        echo "Autoload loaded: <span style='color:green'>✓</span><br>";
    } else {
        echo "<span style='color:red'>AUTOLOAD_FILE not defined or not found</span><br>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>Error loading autoload: " . $e->getMessage() . "</span><br>";
}

echo "<h2>7. Config Test</h2>";
try {
    if (defined('CONFIG_FILE') && file_exists(CONFIG_FILE)) {
        include CONFIG_FILE;
        echo "Config file loaded: <span style='color:green'>✓</span><br>";
        if (isset($cfg)) {
            echo "Config array exists: <span style='color:green'>✓</span><br>";
        }
    } else {
        echo "<span style='color:orange'>CONFIG_FILE not found (may be OK if using defaults)</span><br>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>Error loading config: " . $e->getMessage() . "</span><br>";
}

echo "<h2>8. Common::run() Test</h2>";
try {
    if (class_exists('PhpMyAdmin\Common')) {
        echo "Common class exists: <span style='color:green'>✓</span><br>";
        // Don't actually run it, just check if class exists
    } else {
        echo "<span style='color:red'>Common class not found</span><br>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>Error: " . $e->getMessage() . "</span><br>";
}

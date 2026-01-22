<?php
/**
 * Error test - try to load phpMyAdmin and catch errors
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>phpMyAdmin Error Test</h1>";

// Capture output
ob_start();

try {
    if (! defined('ROOT_PATH')) {
        define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
    }
    
    echo "<h2>Step 1: Loading constants.php</h2>";
    require_once ROOT_PATH . 'libraries/constants.php';
    echo "✓ constants.php loaded<br>";
    
    echo "<h2>Step 2: Loading autoload</h2>";
    if (defined('AUTOLOAD_FILE') && file_exists(AUTOLOAD_FILE)) {
        require AUTOLOAD_FILE;
        echo "✓ autoload.php loaded<br>";
    } else {
        throw new Exception("AUTOLOAD_FILE not found: " . (defined('AUTOLOAD_FILE') ? AUTOLOAD_FILE : 'not defined'));
    }
    
    echo "<h2>Step 3: Loading config</h2>";
    if (defined('CONFIG_FILE') && file_exists(CONFIG_FILE)) {
        include CONFIG_FILE;
        echo "✓ config.inc.php loaded<br>";
    }
    
    echo "<h2>Step 4: Testing Common class</h2>";
    if (class_exists('PhpMyAdmin\Common')) {
        echo "✓ Common class exists<br>";
    } else {
        throw new Exception("Common class not found");
    }
    
    echo "<h2>Step 5: Testing Routing class</h2>";
    if (class_exists('PhpMyAdmin\Routing')) {
        echo "✓ Routing class exists<br>";
    } else {
        throw new Exception("Routing class not found");
    }
    
    echo "<h2>Step 6: Testing Core class</h2>";
    if (class_exists('PhpMyAdmin\Core')) {
        echo "✓ Core class exists<br>";
    } else {
        throw new Exception("Core class not found");
    }
    
    echo "<h2>Step 7: Testing Container Builder</h2>";
    $containerBuilder = PhpMyAdmin\Core::getContainerBuilder();
    if ($containerBuilder) {
        echo "✓ Container builder created<br>";
    } else {
        throw new Exception("Container builder failed");
    }
    
    echo "<h2>Step 8: Testing Config</h2>";
    $config = $containerBuilder->get('config');
    if ($config) {
        echo "✓ Config loaded<br>";
    } else {
        throw new Exception("Config failed");
    }
    
    echo "<h2 style='color:green'>All tests passed!</h2>";
    
} catch (Throwable $e) {
    $output = ob_get_clean();
    echo "<h2 style='color:red'>Error occurred!</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "<h3>Output before error:</h3>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    exit;
}

$output = ob_get_clean();
echo $output;

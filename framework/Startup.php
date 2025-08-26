<?php

// Autoload class function
function autoload($class) {
    $namespaceMap = [
        'Core\\'        => Framework . 'Core/',
        'Helper\\'      => Framework . 'Helper/',
        'Controllers\\' => DIR . 'app/Controllers/',
        'Models\\'      => DIR . 'app/Models/',
    ];

    foreach ($namespaceMap as $prefix => $baseDir) {
        if (strpos($class, $prefix) === 0) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }

    throw new Exception("Class {$class} not found in registered paths.");
}

// Register the autoload function
spl_autoload_register('autoload');

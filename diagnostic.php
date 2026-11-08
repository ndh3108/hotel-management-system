<?php
require_once __DIR__ . '/Public/bootstrap.php';

try {
    $reflector = new ReflectionClass('App\Services\EntityImageService');
    echo "File: " . $reflector->getFileName() . "\n";
    
    $constructor = $reflector->getConstructor();
    if ($constructor) {
        echo "Constructor exists\n";
    }

    $props = $reflector->getProperties();
    foreach ($props as $prop) {
        echo "Property: " . $prop->getName() . " - Type: " . ($prop->getType() ? $prop->getType()->getName() : 'none') . "\n";
    }

    // Try to instantiate
    echo "Attempting to instantiate...\n";
    $instance = new \App\Services\EntityImageService();
    echo "Success!\n";

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

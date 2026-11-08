<?php

define('BASE_PATH', dirname(__DIR__));

// Minimal autoloader for the script
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/App/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Domain\Services\CloudStorageService;
use App\Core\Database;

echo "--- Cloud Storage Seeding Started ---\n";

$cloudinary = new CloudStorageService();
$pdo = Database::getInstance()->getConnection();

$mappings = [
    ['id' => 1, 'file' => 'service_ballroom.png', 'folder' => 'services'],
    ['id' => 2, 'file' => 'service_lounge.png', 'folder' => 'services'],
    ['id' => 3, 'file' => 'service_dining.png', 'folder' => 'services'],
    ['id' => 4, 'file' => 'service_dining.png', 'folder' => 'services'],
    ['id' => 5, 'file' => 'service_meeting.png', 'folder' => 'services'],
    ['id' => 6, 'file' => 'service_meeting.png', 'folder' => 'services'],
    ['id' => 7, 'file' => 'service_dining.png', 'folder' => 'services'],
];

$baseImagePath = BASE_PATH . '/public/assets/image/';

foreach ($mappings as $map) {
    $localPath = $baseImagePath . $map['file'];
    if (!file_exists($localPath)) {
        echo "Error: File not found - {$localPath}\n";
        continue;
    }

    echo "Uploading {$map['file']} for Service ID {$map['id']}... ";
    
    $result = $cloudinary->uploadDetails($localPath, $map['folder']);
    
    if ($result) {
        $url = $result['secure_url'];
        $publicId = $result['public_id'];
        
        // Update or Insert into images table
        $pdo->prepare("DELETE FROM images WHERE entity_type = 'service' AND entity_id = ?")->execute([$map['id']]);
        $pdo->prepare("INSERT INTO images (entity_type, entity_id, image_url, public_id) VALUES ('service', ?, ?, ?)")
            ->execute([$map['id'], $url, $publicId]);
            
        echo "Success! [{$publicId}]\n";
    } else {
        echo "FAILED.\n";
    }
}

echo "--- Seeding Complete ---\n";


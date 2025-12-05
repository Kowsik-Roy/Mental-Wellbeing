<?php
/**
 * Complete permission fix for Laravel on XAMPP
 * Run this file via: php fix-permissions-complete.php
 */

$basePath = __DIR__;

$directories = [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

echo "Fixing Laravel storage permissions...\n\n";

foreach ($directories as $dir) {
    $fullPath = $basePath . '/' . $dir;
    
    // Create directory if it doesn't exist
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0777, true);
        echo "Created: $dir\n";
    }
    
    // Set permissions
    chmod($fullPath, 0777);
    echo "Set permissions (777) on: $dir\n";
    
    // Recursively fix all files and subdirectories
    if (is_dir($fullPath)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                chmod($item->getPathname(), 0777);
            } else {
                chmod($item->getPathname(), 0666);
            }
        }
    }
}

echo "\n✅ Permissions fixed!\n";
echo "All storage directories are now world-writable (777).\n";
echo "\nIf you still get errors, you may need to run:\n";
echo "sudo chown -R _www:staff storage bootstrap/cache\n";
echo "(Replace _www with your Apache user if different)\n";


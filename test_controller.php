<?php
require __DIR__.'/vendor/autoload.php';

use App\Http\Controllers\HabitController;

echo "HabitController exists: " . (class_exists(HabitController::class) ? 'YES' : 'NO') . "\n";
echo "File exists: " . (file_exists(__DIR__.'/app/Http/Controllers/HabitController.php') ? 'YES' : 'NO') . "\n";

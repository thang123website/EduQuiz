<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Blog;

$blog = Blog::first();
echo "Before: " . json_encode($blog->getTranslations('title')) . "\n";
$blog->update(['title' => ['vi' => 'Test replace vi only']]);
$blog = $blog->fresh();
echo "After: " . json_encode($blog->getTranslations('title')) . "\n";

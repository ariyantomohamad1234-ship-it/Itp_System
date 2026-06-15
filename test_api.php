<?php
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Create a fake user
$user = \App\Models\User::find(1); // Admin user

// Test dashboard API
$response = app()->make('App\Http\Controllers\Api\AdminApiController')
    ->dashboard(
        new \Illuminate\Http\Request(['user' => $user])
    );

$data = json_decode($response->getContent(), true);

// Save to file for easy inspection
file_put_contents(__DIR__ . '/test_api_response.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "✅ API response saved to test_api_response.json\n";
echo "Users count: " . count($data['users'] ?? []) . "\n";
echo "Projects count: " . count($data['projects'] ?? []) . "\n";
echo "Recent activity count: " . count($data['recent_activity'] ?? []) . "\n";

// Also output projects to see structure
if (!empty($data['projects'])) {
    echo "\n📋 First project structure:\n";
    echo json_encode($data['projects'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
}

// Output first user to see structure
if (!empty($data['users'])) {
    echo "\n👤 First user structure:\n";
    echo json_encode($data['users'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
}

// Output first activity to see structure
if (!empty($data['recent_activity'])) {
    echo "\n⏰ First activity structure:\n";
    echo json_encode($data['recent_activity'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
}
?>

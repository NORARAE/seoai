<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PagePayload;

$payload = PagePayload::find(4);

if (!$payload) {
    echo "Payload not found\n";
    exit(1);
}

echo "=== EXPORT TESTING ===\n\n";

// Test JSON export
echo "Testing JSON export...\n";
$json = $payload->toExportFormat('json');
echo "JSON length: " . strlen($json) . " bytes\n";
$decoded = json_decode($json);
echo "Valid JSON: " . ($decoded ? 'Yes' : 'No') . "\n";
if ($decoded) {
    echo "Contains title: " . (isset($decoded->content->title) ? 'Yes' : 'No') . "\n";
    echo "Contains body: " . (isset($decoded->content->body_html) ? 'Yes' : 'No') . "\n";
}
echo "\n";

// Test Markdown export
echo "Testing Markdown export...\n";
$md = $payload->toExportFormat('markdown');
echo "Markdown length: " . strlen($md) . " bytes\n";
echo "Contains heading: " . (strpos($md, '# ') !== false ? 'Yes' : 'No') . "\n";
echo "\n";

// Test HTML export
echo "Testing HTML export...\n";
$html = $payload->toExportFormat('html');
echo "HTML length: " . strlen($html) . " bytes\n";
echo "Valid HTML structure: " . (strpos($html, '<!DOCTYPE html>') !== false ? 'Yes' : 'No') . "\n";
echo "\n";

// Test readiness
echo "=== READINESS CHECK ===\n";
echo "Status: " . $payload->status . "\n";
echo "Publish Status: " . $payload->publish_status . "\n";
echo "Has body content: " . (!empty($payload->body_content) ? 'Yes' : 'No') . "\n";
echo "Has title: " . (!empty($payload->title) ? 'Yes' : 'No') . "\n";
echo "Is Ready to Publish: " . ($payload->isReadyToPublish() ? 'Yes' : 'No') . "\n";
echo "\n";

// Preview first 300 chars of JSON
echo "=== JSON PREVIEW ===\n";
echo substr($json, 0, 500) . "...\n";

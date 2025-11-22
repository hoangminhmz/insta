<?php
require_once('config.php');
header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST CAROUSEL PROMPT ===\n\n";

$testContent = "Sleep is essential for health. Poor sleep affects your immune system, memory, and mood. Getting 7-9 hours of quality sleep helps your body repair and recharge. Create a consistent sleep schedule and avoid screens before bed.";

require_once('generate.php');

echo "Testing with actual carousel prompt...\n";
echo "Content length: " . strlen($testContent) . " chars\n\n";

$prompt = buildCarouselPrompt($testContent, 'Health & Wellness', 'Educational', 'testuser');

echo "PROMPT LENGTH: " . strlen($prompt) . " chars\n";
echo "PROMPT (first 500 chars):\n";
echo substr($prompt, 0, 500) . "...\n\n";

echo "Making API call...\n";

try {
    $result = callGeminiAPI($prompt);
    echo "\n=== SUCCESS ===\n";
    echo "Carousel data received!\n";
    echo "Slides: " . count($result) . "\n";
    echo "\nSlide keys:\n";
    print_r(array_keys($result));

    echo "\nSlide 1 sample:\n";
    print_r($result['slide1']);

} catch (Exception $e) {
    echo "\n=== ERROR ===\n";
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>

<?php
require_once('config.php');
header('Content-Type: text/plain; charset=utf-8');

echo "=== SIMPLE GEMINI API TEST ===\n\n";

// Step 1: Check config
echo "STEP 1: Configuration\n";
echo "---------------------\n";
echo "API Key: " . (GEMINI_API_KEY !== 'YOUR_GEMINI_API_KEY_HERE' ? 'SET' : 'NOT SET') . "\n";
echo "API URL: " . GEMINI_API_URL . "\n\n";

if (GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_HERE') {
    die("ERROR: Please set API key in config.php\n");
}

// Step 2: Simple test prompt
echo "STEP 2: Test Prompt\n";
echo "---------------------\n";
$testPrompt = 'Return ONLY this JSON with no markdown: {"status":"ok","test":"success"}';
echo "Prompt: $testPrompt\n\n";

// Step 3: Build payload WITH responseMimeType
echo "STEP 3: Build Payload\n";
echo "---------------------\n";
$payload = [
    'contents' => [
        ['parts' => [['text' => $testPrompt]]]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 1024,
        'responseMimeType' => 'application/json'
    ]
];
echo "Payload built with responseMimeType=application/json\n\n";

// Step 4: Make API call
echo "STEP 4: API Call\n";
echo "---------------------\n";
$url = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n\n";
    die();
}
echo "\n";

// Step 5: Show raw response
echo "STEP 5: Raw Response (first 1000 chars)\n";
echo "---------------------\n";
echo substr($response, 0, 1000) . "\n\n";

// Step 6: Parse response
echo "STEP 6: Parse API Response\n";
echo "---------------------\n";
$result = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "ERROR: Cannot parse API response as JSON\n";
    echo "Error: " . json_last_error_msg() . "\n";
    die();
}
echo "API response parsed OK\n\n";

// Step 7: Extract generated text
echo "STEP 7: Extract Generated Text\n";
echo "---------------------\n";
if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo "ERROR: No text found in response\n";
    echo "Response structure:\n";
    print_r(array_keys($result));
    die();
}

$generatedText = $result['candidates'][0]['content']['parts'][0]['text'];
echo "Generated text:\n";
echo "---------------------\n";
echo $generatedText . "\n";
echo "---------------------\n";
echo "Length: " . strlen($generatedText) . " chars\n";
echo "First char: '" . substr($generatedText, 0, 1) . "' (ASCII: " . ord(substr($generatedText, 0, 1)) . ")\n";
echo "Last char: '" . substr($generatedText, -1) . "' (ASCII: " . ord(substr($generatedText, -1)) . ")\n\n";

// Step 8: Try to parse as JSON
echo "STEP 8: Parse Generated Text as JSON\n";
echo "---------------------\n";
$jsonData = json_decode($generatedText, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "ERROR: Cannot parse generated text as JSON\n";
    echo "Error: " . json_last_error_msg() . "\n";
    echo "\nGenerated text (with visible whitespace):\n";
    echo str_replace(["\n", "\r", "\t"], ['\\n', '\\r', '\\t'], $generatedText) . "\n\n";

    echo "Trying to clean...\n";
    // Try cleaning
    $cleaned = preg_replace('/```[a-zA-Z]*\s*/s', '', $generatedText);
    $cleaned = preg_replace('/```\s*$/s', '', $cleaned);
    $cleaned = trim($cleaned);

    echo "Cleaned text:\n";
    echo $cleaned . "\n\n";

    $jsonData2 = json_decode($cleaned, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "SUCCESS after cleaning!\n";
        print_r($jsonData2);
    } else {
        echo "STILL FAILED: " . json_last_error_msg() . "\n";
    }
} else {
    echo "SUCCESS! JSON parsed:\n";
    print_r($jsonData);
}

echo "\n=== TEST COMPLETE ===\n";
?>

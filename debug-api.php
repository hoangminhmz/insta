<?php
/**
 * Debug Tool for Gemini API Testing
 * Test if Gemini 2.5 Flash is working and see raw responses
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load config
require_once('config.php');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Gemini API Debug Tool</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        h1 { color: #4ec9b0; }
        h2 { color: #dcdcaa; margin-top: 30px; }
        .section { background: #252526; padding: 15px; margin: 15px 0; border-left: 3px solid #007acc; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #ce9178; }
        pre { background: #1e1e1e; padding: 15px; overflow-x: auto; border: 1px solid #3c3c3c; }
        .button { background: #007acc; color: white; padding: 10px 20px; border: none; cursor: pointer; margin: 5px; }
        .button:hover { background: #005a9e; }
    </style>
</head>
<body>";

echo "<h1>🔍 Gemini API Debug Tool</h1>";

// Display current configuration
echo "<div class='section'>";
echo "<h2>📋 Current Configuration</h2>";
echo "<strong>API Key:</strong> " . (GEMINI_API_KEY !== 'YOUR_GEMINI_API_KEY_HERE' ? '<span class="success">✓ Configured</span>' : '<span class="error">✗ Not configured</span>') . "<br>";
echo "<strong>Model URL:</strong> " . htmlspecialchars(GEMINI_API_URL) . "<br>";
$modelName = preg_match('/models\/([^:]+)/', GEMINI_API_URL, $matches) ? $matches[1] : 'Unknown';
echo "<strong>Model Name:</strong> " . htmlspecialchars($modelName) . "<br>";
echo "</div>";

// Check if API key is set
if (GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_HERE') {
    echo "<div class='section error'>";
    echo "<h2>❌ Error: API Key Not Set</h2>";
    echo "Please configure your Gemini API key in <code>config.php</code>";
    echo "</div>";
    echo "</body></html>";
    exit;
}

// Test API call
echo "<div class='section'>";
echo "<h2>🚀 Testing API Call</h2>";

$testPrompt = "Return only this JSON: {\"test\": \"success\", \"message\": \"API is working\"}";

echo "<strong>Test Prompt:</strong><br>";
echo "<pre>" . htmlspecialchars($testPrompt) . "</pre>";

// Prepare payload
$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => $testPrompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 1024
    ]
];

echo "<strong>Payload:</strong><br>";
echo "<pre>" . htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT)) . "</pre>";

// Make API call
$url = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$startTime = microtime(true);
$response = curl_exec($ch);
$endTime = microtime(true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

$executionTime = round(($endTime - $startTime) * 1000, 2);

echo "<strong>Execution Time:</strong> {$executionTime}ms<br>";
echo "<strong>HTTP Status Code:</strong> ";

if ($httpCode === 200) {
    echo "<span class='success'>$httpCode ✓</span><br>";
} else {
    echo "<span class='error'>$httpCode ✗</span><br>";
}

if ($curlError) {
    echo "<span class='error'><strong>cURL Error:</strong> " . htmlspecialchars($curlError) . "</span><br>";
}

echo "</div>";

// Display raw response
echo "<div class='section'>";
echo "<h2>📄 Raw API Response</h2>";
echo "<pre>" . htmlspecialchars(substr($response, 0, 2000)) . "</pre>";
if (strlen($response) > 2000) {
    echo "<p class='warning'>Response truncated to 2000 characters. Full length: " . strlen($response) . " chars</p>";
}
echo "</div>";

// Parse and display structured response
if ($httpCode === 200) {
    $result = json_decode($response, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<div class='section'>";
        echo "<h2>🔍 Parsed Response Structure</h2>";
        echo "<pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
        echo "</div>";

        // Extract generated text
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];

            echo "<div class='section success'>";
            echo "<h2>✅ Generated Text</h2>";
            echo "<pre>" . htmlspecialchars($generatedText) . "</pre>";
            echo "</div>";

            // Try to parse as JSON
            $jsonData = json_decode($generatedText, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<div class='section success'>";
                echo "<h2>✅ JSON Parsing: SUCCESS</h2>";
                echo "<pre>" . htmlspecialchars(json_encode($jsonData, JSON_PRETTY_PRINT)) . "</pre>";
                echo "</div>";
            } else {
                echo "<div class='section error'>";
                echo "<h2>❌ JSON Parsing: FAILED</h2>";
                echo "<strong>Error:</strong> " . json_last_error_msg() . "<br>";
                echo "<strong>First 500 chars of text:</strong><br>";
                echo "<pre>" . htmlspecialchars(substr($generatedText, 0, 500)) . "</pre>";
                echo "</div>";
            }
        } else {
            echo "<div class='section error'>";
            echo "<h2>❌ No Generated Text Found</h2>";
            echo "Expected structure not found in response.";
            echo "</div>";
        }

    } else {
        echo "<div class='section error'>";
        echo "<h2>❌ Failed to Parse Response as JSON</h2>";
        echo "<strong>Error:</strong> " . json_last_error_msg();
        echo "</div>";
    }
} else {
    echo "<div class='section error'>";
    echo "<h2>❌ API Request Failed</h2>";
    echo "HTTP Status: $httpCode<br>";
    echo "Response: " . htmlspecialchars(substr($response, 0, 500));
    echo "</div>";
}

// Test with responseMimeType
echo "<div class='section'>";
echo "<h2>🧪 Test with responseMimeType='application/json'</h2>";
echo "<form method='post' action='?test=json'>";
echo "<button type='submit' class='button'>Run Test with JSON MIME Type</button>";
echo "</form>";

if (isset($_GET['test']) && $_GET['test'] === 'json') {
    $payload['generationConfig']['responseMimeType'] = 'application/json';

    echo "<strong>Testing with responseMimeType...</strong><br>";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response2 = curl_exec($ch);
    $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "<strong>HTTP Status:</strong> $httpCode2<br>";
    echo "<strong>Response:</strong><br>";
    echo "<pre>" . htmlspecialchars(substr($response2, 0, 1000)) . "</pre>";
}
echo "</div>";

// Recommendations
echo "<div class='section'>";
echo "<h2>💡 Recommendations</h2>";
if ($httpCode === 200) {
    echo "<span class='success'>✓ API is working!</span><br><br>";
    echo "If JSON parsing failed:<br>";
    echo "1. The model might be adding markdown formatting<br>";
    echo "2. Try using responseMimeType='application/json' (test above)<br>";
    echo "3. Improve the prompt to explicitly request clean JSON<br>";
} else {
    echo "<span class='error'>✗ API is not working</span><br><br>";
    echo "Possible issues:<br>";
    echo "1. Model name might be incorrect (check available models)<br>";
    echo "2. API key might be invalid<br>";
    echo "3. Model might not be available in your region<br>";
    echo "4. Try alternative models: gemini-2.0-flash-exp, gemini-1.5-pro<br>";
}
echo "</div>";

echo "</body></html>";
?>

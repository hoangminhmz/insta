<?php
/**
 * Test Carousel Prompt with Gemini API
 */
require_once('config.php');
header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST CAROUSEL PROMPT ===\n\n";

// Test content
$testContent = "Sleep is essential for health. Poor sleep affects your immune system, memory, and mood. Getting 7-9 hours of quality sleep helps your body repair and recharge. Create a consistent sleep schedule and avoid screens before bed.";

echo "Testing with actual carousel prompt...\n";
echo "Content length: " . strlen($testContent) . " chars\n\n";

// Build carousel prompt (copied from generate.php)
function buildCarouselPrompt($content, $niche, $tone, $username) {
    return <<<PROMPT
You are an expert Instagram content creator specializing in carousel posts. Analyze the following blog content and structure it into a highly engaging 10-slide Instagram carousel.

**BLOG CONTENT:**
$content

**CONTENT NICHE:** $niche
**TONE:** $tone
**USERNAME:** @$username

**OUTPUT REQUIREMENTS:**

You MUST respond with ONLY valid JSON (no markdown, no code blocks, no additional text). Use this EXACT structure:

{
  "slide1": {
    "type": "hook",
    "text": "Attention-grabbing question or bold statement (max 15 words)",
    "keywords": ["word1", "word2"],
    "emoji": "🔥"
  },
  "slide2": {
    "type": "content",
    "heading": "Main point heading (5-8 words)",
    "body": "Clear explanation (20-30 words)",
    "keywords": ["highlight1", "highlight2"]
  },
  "slide3": {
    "type": "content",
    "heading": "Problem explanation (5-8 words)",
    "body": "Why this matters (20-30 words)",
    "keywords": ["key", "terms"]
  },
  "slide4": {
    "type": "content",
    "heading": "Root cause heading (5-8 words)",
    "body": "Deep insight explanation (20-30 words)",
    "keywords": ["important", "concepts"]
  },
  "slide5": {
    "type": "content",
    "heading": "Science/mechanism (5-8 words)",
    "body": "How it works explanation (20-30 words)",
    "keywords": ["technical", "terms"]
  },
  "slide6": {
    "type": "content",
    "heading": "Body's response (5-8 words)",
    "body": "What happens next (20-30 words)",
    "keywords": ["key", "points"]
  },
  "slide7": {
    "type": "list",
    "heading": "Solution steps (5-8 words)",
    "items": [
      "Action step 1 (10-15 words)",
      "Action step 2 (10-15 words)",
      "Action step 3 (10-15 words)"
    ]
  },
  "slide8": {
    "type": "list",
    "heading": "More solutions (5-8 words)",
    "items": [
      "Practical tip 1 (10-15 words)",
      "Practical tip 2 (10-15 words)",
      "Practical tip 3 (10-15 words)"
    ]
  },
  "slide9": {
    "type": "bonus",
    "heading": "Emergency Hack / Pro Tip",
    "body": "Quick actionable advice (25-35 words)",
    "keywords": ["bonus", "tip"]
  },
  "slide10": {
    "type": "cta",
    "text": "Share this with someone who needs to see it!",
    "username": "@$username",
    "hashtags": "#$niche"
  }
}

**CONTENT RULES:**
1. Slide 1: Hook with relatable problem/question
2. Slides 2-3: Explain the problem and why it matters
3. Slides 4-6: Deep dive into causes, science, mechanisms
4. Slides 7-8: Actionable solutions and practical steps
5. Slide 9: Bonus tip, emergency hack, or key takeaway
6. Slide 10: Strong CTA with username branding

**STYLE GUIDELINES:**
- Keep text concise and scannable
- Use active voice and direct language
- Highlight 2-3 keywords per slide for emphasis
- Use emojis sparingly (1 per slide maximum)
- Make each slide valuable on its own
- Ensure smooth flow between slides
- Focus on actionable insights
- Avoid jargon unless explaining it

**TONE:** Use a $tone tone that resonates with $niche audience.

CRITICAL INSTRUCTIONS:
- Output ONLY the JSON object
- NO markdown code blocks (no ```)
- NO explanations before or after the JSON
- NO additional text
- Start directly with { and end with }
- Ensure valid JSON syntax
PROMPT;
}

$prompt = buildCarouselPrompt($testContent, 'Health & Wellness', 'Educational', 'testuser');

echo "PROMPT LENGTH: " . strlen($prompt) . " chars\n";
echo "PROMPT (first 500 chars):\n";
echo substr($prompt, 0, 500) . "...\n\n";

// Make API call
echo "Making API call...\n\n";

$apiKey = GEMINI_API_KEY;
$url = GEMINI_API_URL . '?key=' . $apiKey;

$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 8192, // Increased for full 10-slide carousel
        'responseMimeType' => 'application/json'
    ],
    'safetySettings' => [
        [
            'category' => 'HARM_CATEGORY_HARASSMENT',
            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
        ],
        [
            'category' => 'HARM_CATEGORY_HATE_SPEECH',
            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$startTime = microtime(true);
$response = curl_exec($ch);
$endTime = microtime(true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

$executionTime = round(($endTime - $startTime) * 1000, 2);

echo "Execution time: {$executionTime}ms\n";
echo "HTTP Code: $httpCode\n\n";

if ($error) {
    echo "cURL Error: $error\n";
    exit;
}

if ($httpCode !== 200) {
    echo "=== ERROR ===\n";
    echo "HTTP $httpCode\n";
    echo "Response:\n" . substr($response, 0, 500) . "\n";
    exit;
}

// Parse response
$result = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "=== ERROR ===\n";
    echo "Cannot parse API response: " . json_last_error_msg() . "\n";
    echo "Response:\n" . substr($response, 0, 500) . "\n";
    exit;
}

// Extract generated text
if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo "=== ERROR ===\n";
    echo "No text in API response\n";
    print_r($result);
    exit;
}

$generatedText = $result['candidates'][0]['content']['parts'][0]['text'];

echo "Generated text length: " . strlen($generatedText) . " chars\n";
echo "First 200 chars:\n" . substr($generatedText, 0, 200) . "\n\n";

// Parse as JSON
$carouselData = json_decode($generatedText, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "=== ERROR: JSON PARSING FAILED ===\n";
    echo "Error: " . json_last_error_msg() . "\n";
    echo "\nGenerated text (first 500 chars):\n";
    echo substr($generatedText, 0, 500) . "\n\n";

    // Try cleaning
    echo "Trying to clean...\n";
    $cleaned = preg_replace('/```[a-zA-Z]*\s*/s', '', $generatedText);
    $cleaned = preg_replace('/```\s*$/s', '', $cleaned);
    $cleaned = trim($cleaned);

    $firstBrace = strpos($cleaned, '{');
    $lastBrace = strrpos($cleaned, '}');
    if ($firstBrace !== false && $lastBrace !== false) {
        $cleaned = substr($cleaned, $firstBrace, $lastBrace - $firstBrace + 1);
    }

    $carouselData = json_decode($cleaned, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "SUCCESS after cleaning!\n\n";
    } else {
        echo "STILL FAILED: " . json_last_error_msg() . "\n";
        exit;
    }
}

echo "=== SUCCESS ===\n";
echo "Carousel data received!\n";
echo "Slides: " . count($carouselData) . "\n\n";

echo "Slide keys:\n";
print_r(array_keys($carouselData));

echo "\nSlide 1 structure:\n";
print_r($carouselData['slide1']);

echo "\nSlide 10 structure:\n";
print_r($carouselData['slide10']);

echo "\n=== TEST COMPLETE ===\n";
?>

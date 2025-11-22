<?php
/**
 * Instagram Carousel Generator - API Handler
 * Processes content with Google Gemini AI
 */

header('Content-Type: application/json');
require_once('config.php');

// Set CORS headers
setCorsHeaders();

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Check rate limiting
if (!checkRateLimit()) {
    http_response_code(429);
    echo json_encode([
        'error' => 'Rate limit exceeded',
        'message' => 'You have reached the maximum number of generations. Please try again in 1 hour.'
    ]);
    exit();
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['content']) || empty($data['content'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Content is required']);
    exit();
}

// Sanitize and validate inputs
$content = substr(sanitizeInput($data['content']), 0, MAX_CONTENT_LENGTH);
$niche = sanitizeInput($data['niche'] ?? 'General');
$tone = sanitizeInput($data['tone'] ?? 'Educational');
$username = sanitizeInput($data['username'] ?? 'yourusername');

// Build the AI prompt
$prompt = buildCarouselPrompt($content, $niche, $tone, $username);

// Call Gemini API
try {
    $response = callGeminiAPI($prompt);

    // Increment generation count
    incrementGenerationCount();

    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $response
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API Error',
        'message' => $e->getMessage()
    ]);
}

/**
 * Build the structured prompt for Gemini AI
 */
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
1. Slide 1: Hook that creates curiosity or addresses pain point
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

/**
 * Call Google Gemini API
 */
function callGeminiAPI($prompt) {
    $apiKey = GEMINI_API_KEY;

    if ($apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
        throw new Exception('Please configure your Gemini API key in config.php');
    }

    $url = GEMINI_API_URL . '?key=' . $apiKey;

    // Prepare request payload
    $payload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048
            // Note: responseMimeType may not be supported in all models
            // Removed to ensure compatibility
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

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // Check for cURL errors
    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }

    // Check HTTP response code
    if ($httpCode !== 200) {
        // Log the full error response
        error_log('Gemini API Error - HTTP ' . $httpCode);
        error_log('Response: ' . substr($response, 0, 1000));
        throw new Exception('API returned HTTP ' . $httpCode . ': ' . substr($response, 0, 200));
    }

    // Parse response
    $result = json_decode($response, true);

    // Check if response is valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Failed to decode API response as JSON: ' . json_last_error_msg());
        error_log('Raw response: ' . substr($response, 0, 1000));
        throw new Exception('Invalid JSON response from API. Response starts with: ' . substr($response, 0, 100));
    }

    // Check if response has expected structure
    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        // Log the actual structure received
        error_log('Unexpected API response structure');
        error_log('Response keys: ' . json_encode(array_keys($result)));
        if (isset($result['error'])) {
            error_log('API Error: ' . json_encode($result['error']));
            throw new Exception('API Error: ' . ($result['error']['message'] ?? 'Unknown error'));
        }
        throw new Exception('Unexpected API response format. Please check API key and model availability.');
    }

    // Extract the JSON from the response
    $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];

    // Clean up the response - remove all markdown code blocks
    // Handle various markdown formats: ```json, ```JSON, ``` json, etc.
    $generatedText = preg_replace('/```[a-zA-Z]*\s*/s', '', $generatedText);
    $generatedText = preg_replace('/```\s*$/s', '', $generatedText);
    $generatedText = trim($generatedText);

    // Try to extract JSON if there's extra text
    // Find first { and last } to extract pure JSON
    $firstBrace = strpos($generatedText, '{');
    $lastBrace = strrpos($generatedText, '}');

    if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
        $generatedText = substr($generatedText, $firstBrace, $lastBrace - $firstBrace + 1);
    }

    // Parse the JSON
    $carouselData = json_decode($generatedText, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        // Log the problematic response for debugging
        error_log('Gemini API Response (raw): ' . substr($result['candidates'][0]['content']['parts'][0]['text'], 0, 500));
        error_log('Cleaned text: ' . substr($generatedText, 0, 500));
        error_log('JSON Error: ' . json_last_error_msg());

        throw new Exception('Failed to parse AI response as JSON: ' . json_last_error_msg() . '. Please try again or check the API response.');
    }

    return $carouselData;
}

// Auto cleanup old files
cleanupOldFiles();
?>

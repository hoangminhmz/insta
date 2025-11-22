<?php
/**
 * Instagram Carousel Generator - Configuration File
 *
 * SECURITY: Keep this file ABOVE public_html directory
 * Never commit API keys to version control
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// API Configuration
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');

// Gemini Model Options (uncomment the one you want to use):
// PRIMARY: Gemini 2.5 Flash (latest model)
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent');

// Fallback 1: Gemini 2.0 Flash Experimental
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent');

// Fallback 2: Gemini 1.5 Pro
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent');

// Fallback 3: Gemini 1.5 Flash (stable)
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent');

// Application Settings
define('MAX_CONTENT_LENGTH', 10000); // Max characters for input
define('MAX_GENERATIONS_PER_SESSION', 10); // Rate limiting
define('SESSION_TIMEOUT', 3600); // 1 hour

// File Paths
define('BASE_PATH', __DIR__);
define('OUTPUT_PATH', BASE_PATH . '/output');
define('TEMP_FILE_LIFETIME', 3600); // 1 hour in seconds

// Image Settings
define('CANVAS_WIDTH', 1080);
define('CANVAS_HEIGHT', 1350);
define('IMAGE_QUALITY', 90);
define('IMAGE_FORMAT', 'png');

// Allowed Origins (CORS)
define('ALLOWED_ORIGINS', [
    'http://localhost',
    'https://yourdomain.com'
]);

// Database (if needed in future)
define('DB_HOST', 'localhost');
define('DB_NAME', 'carousel_db');
define('DB_USER', 'db_user');
define('DB_PASS', 'db_password');

// Utility Functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function checkRateLimit() {
    session_start();

    if (!isset($_SESSION['generation_count'])) {
        $_SESSION['generation_count'] = 0;
        $_SESSION['generation_start_time'] = time();
    }

    // Reset counter after 1 hour
    if (time() - $_SESSION['generation_start_time'] > SESSION_TIMEOUT) {
        $_SESSION['generation_count'] = 0;
        $_SESSION['generation_start_time'] = time();
    }

    if ($_SESSION['generation_count'] >= MAX_GENERATIONS_PER_SESSION) {
        return false;
    }

    return true;
}

function incrementGenerationCount() {
    session_start();
    $_SESSION['generation_count'] = ($_SESSION['generation_count'] ?? 0) + 1;
}

function setCorsHeaders() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, ALLOWED_ORIGINS)) {
        header("Access-Control-Allow-Origin: $origin");
    }

    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
}

// Auto-cleanup old files
function cleanupOldFiles() {
    $files = glob(OUTPUT_PATH . '/*');
    $now = time();

    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= TEMP_FILE_LIFETIME) {
                unlink($file);
            }
        }
    }
}

?>

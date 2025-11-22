<?php
/**
 * Instagram Carousel Generator - Export Handler
 * Handles image data from frontend and creates ZIP file
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

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['images']) || !is_array($data['images']) || count($data['images']) === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'No images provided']);
    exit();
}

try {
    // Create unique session ID for this export
    $sessionId = uniqid('carousel_', true);
    $sessionDir = OUTPUT_PATH . '/' . $sessionId;

    // Create session directory
    if (!file_exists($sessionDir)) {
        mkdir($sessionDir, 0755, true);
    }

    // Process and save each image
    $savedFiles = [];
    foreach ($data['images'] as $index => $imageData) {
        $fileName = sprintf('slide-%02d.png', $index + 1);
        $filePath = $sessionDir . '/' . $fileName;

        // Extract base64 data
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData['data'], $type)) {
            $imageData['data'] = substr($imageData['data'], strpos($imageData['data'], ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            $imageData['data'] = base64_decode($imageData['data']);

            if ($imageData['data'] === false) {
                throw new Exception('Base64 decode failed for image ' . ($index + 1));
            }

            // Save image file
            if (file_put_contents($filePath, $imageData['data']) === false) {
                throw new Exception('Failed to save image ' . ($index + 1));
            }

            $savedFiles[] = [
                'name' => $fileName,
                'path' => $filePath
            ];
        } else {
            throw new Exception('Invalid image data format for image ' . ($index + 1));
        }
    }

    // Create ZIP file
    $zipFileName = 'instagram-carousel-' . date('Y-m-d-His') . '.zip';
    $zipFilePath = OUTPUT_PATH . '/' . $zipFileName;

    $zip = new ZipArchive();
    if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
        throw new Exception('Failed to create ZIP file');
    }

    // Add all images to ZIP
    foreach ($savedFiles as $file) {
        $zip->addFile($file['path'], $file['name']);
    }

    $zip->close();

    // Clean up individual image files
    foreach ($savedFiles as $file) {
        unlink($file['path']);
    }
    rmdir($sessionDir);

    // Return download URL
    $downloadUrl = 'download.php?file=' . urlencode($zipFileName);

    echo json_encode([
        'success' => true,
        'downloadUrl' => $downloadUrl,
        'fileName' => $zipFileName,
        'fileCount' => count($savedFiles)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Export failed',
        'message' => $e->getMessage()
    ]);
}
?>

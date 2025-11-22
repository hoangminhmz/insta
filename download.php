<?php
/**
 * Instagram Carousel Generator - Download Handler
 * Serves ZIP files for download
 */

require_once('config.php');

// Get filename from query string
$fileName = $_GET['file'] ?? '';

// Validate filename
if (empty($fileName)) {
    http_response_code(400);
    die('No file specified');
}

// Sanitize filename to prevent directory traversal
$fileName = basename($fileName);

// Check if file exists
$filePath = OUTPUT_PATH . '/' . $fileName;

if (!file_exists($filePath)) {
    http_response_code(404);
    die('File not found');
}

// Verify it's a ZIP file
if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'zip') {
    http_response_code(403);
    die('Invalid file type');
}

// Set headers for download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output file
readfile($filePath);

// Delete file after download (optional - or let cleanup.php handle it)
// unlink($filePath);

exit();
?>

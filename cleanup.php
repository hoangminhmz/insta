<?php
/**
 * Instagram Carousel Generator - Cleanup Script
 * Removes temporary files older than configured lifetime
 *
 * Run this script via cron job:
 * 0 * * * * php /path/to/carousel-generator/cleanup.php
 * (Runs every hour)
 */

require_once('config.php');

// Log file for cleanup operations
$logFile = OUTPUT_PATH . '/cleanup.log';

// Function to log messages
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// Start cleanup
logMessage('=== Cleanup script started ===');

try {
    $deletedCount = 0;
    $errorCount = 0;
    $totalSize = 0;

    // Check if output directory exists
    if (!is_dir(OUTPUT_PATH)) {
        logMessage('Output directory does not exist: ' . OUTPUT_PATH);
        exit(1);
    }

    // Get all files in output directory
    $files = glob(OUTPUT_PATH . '/*');
    $now = time();

    foreach ($files as $file) {
        // Skip log file and directories
        if ($file === $logFile || is_dir($file)) {
            continue;
        }

        // Check file age
        $fileAge = $now - filemtime($file);

        if ($fileAge >= TEMP_FILE_LIFETIME) {
            $fileSize = filesize($file);
            $fileName = basename($file);

            // Attempt to delete file
            if (unlink($file)) {
                $deletedCount++;
                $totalSize += $fileSize;
                logMessage("Deleted: $fileName (Age: " . round($fileAge / 60) . " minutes, Size: " . formatBytes($fileSize) . ")");
            } else {
                $errorCount++;
                logMessage("ERROR: Failed to delete $fileName");
            }
        }
    }

    // Clean up empty session directories
    $directories = glob(OUTPUT_PATH . '/carousel_*', GLOB_ONLYDIR);
    foreach ($directories as $dir) {
        // Check if directory is empty
        $dirFiles = scandir($dir);
        if (count($dirFiles) <= 2) { // Only . and ..
            if (rmdir($dir)) {
                logMessage("Removed empty directory: " . basename($dir));
            }
        }
    }

    // Log summary
    logMessage("Cleanup completed: $deletedCount files deleted, " . formatBytes($totalSize) . " freed");

    if ($errorCount > 0) {
        logMessage("WARNING: $errorCount files could not be deleted");
    }

    logMessage('=== Cleanup script finished ===');

    // Keep log file size manageable (max 1MB)
    if (file_exists($logFile) && filesize($logFile) > 1048576) {
        $logContent = file_get_contents($logFile);
        $logLines = explode("\n", $logContent);
        $recentLines = array_slice($logLines, -1000); // Keep last 1000 lines
        file_put_contents($logFile, implode("\n", $recentLines));
        logMessage('Log file trimmed to last 1000 lines');
    }

} catch (Exception $e) {
    logMessage('FATAL ERROR: ' . $e->getMessage());
    exit(1);
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// If run from command line, output summary
if (php_sapi_name() === 'cli') {
    echo "Cleanup completed successfully\n";
    echo "Check log file: $logFile\n";
}
?>

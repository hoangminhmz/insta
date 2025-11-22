<?php
/**
 * View Error Logs - Simple log viewer
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error Logs Viewer</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }
        h1 { color: #4ec9b0; }
        pre {
            background: #252526;
            padding: 15px;
            overflow-x: auto;
            border-left: 3px solid #007acc;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .error { color: #f48771; }
        .info { color: #4ec9b0; }
        .button {
            background: #007acc;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin: 10px 5px 10px 0;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover { background: #005a9e; }
    </style>
</head>
<body>

<h1>🔍 Error Logs Viewer</h1>

<a href="test-simple.php" class="button">Run Simple Test</a>
<a href="debug-api.php" class="button">Run Full Debug</a>
<a href="?refresh=1" class="button">Refresh Logs</a>

<h2>PHP Error Log</h2>
<?php
// Try different log locations
$possibleLogs = [
    '/var/log/apache2/error.log',
    '/var/log/httpd/error_log',
    ini_get('error_log'),
    'error_log',
    '../error_log'
];

echo "<p class='info'>Looking for logs in:</p><pre>";
foreach ($possibleLogs as $log) {
    echo "$log\n";
}
echo "</pre>";

$logFound = false;
foreach ($possibleLogs as $logFile) {
    if ($logFile && file_exists($logFile) && is_readable($logFile)) {
        echo "<p class='info'>Found log: $logFile</p>";

        // Get last 100 lines
        $lines = file($logFile);
        if ($lines) {
            $recentLines = array_slice($lines, -100);

            // Filter for Gemini-related errors
            $geminiLines = array_filter($recentLines, function($line) {
                return stripos($line, 'gemini') !== false ||
                       stripos($line, 'json') !== false ||
                       stripos($line, 'carousel') !== false;
            });

            if (count($geminiLines) > 0) {
                echo "<h3>Gemini-related logs (last 100 lines filtered):</h3>";
                echo "<pre>";
                echo htmlspecialchars(implode('', $geminiLines));
                echo "</pre>";
            } else {
                echo "<h3>Recent logs (last 20 lines):</h3>";
                echo "<pre>";
                echo htmlspecialchars(implode('', array_slice($recentLines, -20)));
                echo "</pre>";
            }

            $logFound = true;
            break;
        }
    }
}

if (!$logFound) {
    echo "<p class='error'>No readable error log found.</p>";
    echo "<p>Try checking your server's error log location or enable error logging in config.php</p>";
}
?>

<h2>Generate a Test Error</h2>
<p>Click button to generate test carousel and see errors:</p>
<form method="post">
    <button type="submit" name="test" class="button">Test Carousel Generation</button>
</form>

<?php
if (isset($_POST['test'])) {
    echo "<h3>Test Result:</h3>";
    echo "<pre>";

    // Make test API call
    $testData = [
        'content' => 'This is a test article about health and wellness. It discusses the importance of sleep and exercise.',
        'niche' => 'Health & Wellness',
        'tone' => 'Educational',
        'username' => 'testuser'
    ];

    $ch = curl_init('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/generate.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: $httpCode\n";
    echo "Response:\n";
    echo htmlspecialchars($response);
    echo "</pre>";

    echo "<p class='info'>Check the logs above for detailed error messages</p>";
}
?>

</body>
</html>

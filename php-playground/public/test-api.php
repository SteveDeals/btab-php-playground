<?php
require_once 'api-helper.php';

// Test API connection
$testResults = [];

// Test 1: Check if API key is configured
$testResults[] = [
    'name' => 'API Key Configuration',
    'status' => isApiKeyConfigured() ? 'pass' : 'fail',
    'message' => isApiKeyConfigured() ? 'API key is configured' : 'API key is not set in config.php'
];

// Test 2: Try to fetch products
if (isApiKeyConfigured()) {
    $response = getMyProducts();
    $testResults[] = [
        'name' => 'API Connection',
        'status' => ($response['http_code'] == 200) ? 'pass' : 'fail',
        'message' => 'HTTP ' . $response['http_code'] . ' - ' . ($response['error'] ?? 'Connection successful'),
        'data' => $response
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test - Btab PHP Playground</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }
        header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #667eea; }
        .nav { margin-top: 10px; }
        .nav a {
            color: #667eea;
            text-decoration: none;
            margin-right: 15px;
        }
        .test-result {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid;
        }
        .test-result.pass { border-color: #28a745; }
        .test-result.fail { border-color: #dc3545; }
        .test-result h3 {
            margin-bottom: 10px;
        }
        .test-result.pass h3::before { content: '✓ '; color: #28a745; }
        .test-result.fail h3::before { content: '✗ '; color: #dc3545; }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
            font-size: 0.9em;
        }
        .info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>API Connection Test</h1>
            <div class="nav">
                <a href="index.php">← Home</a>
                <a href="products.php">Products</a>
            </div>
        </header>

        <div class="info">
            <strong>Testing connection to:</strong> <?php echo BTAB_API_URL; ?>
        </div>

        <?php foreach ($testResults as $test): ?>
            <div class="test-result <?php echo $test['status']; ?>">
                <h3><?php echo htmlspecialchars($test['name']); ?></h3>
                <p><?php echo htmlspecialchars($test['message']); ?></p>
                
                <?php if (isset($test['data']) && DEBUG_MODE): ?>
                    <details>
                        <summary style="cursor: pointer; margin-top: 10px;">Show API Response</summary>
                        <div class="code"><?php echo htmlspecialchars(json_encode($test['data'], JSON_PRETTY_PRINT)); ?></div>
                    </details>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="info" style="margin-top: 30px;">
            <h3>Troubleshooting Tips:</h3>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Make sure you've registered at <a href="https://dashboard.btab.app/register" target="_blank">dashboard.btab.app</a></li>
                <li>Copy your API key from the dashboard after logging in</li>
                <li>Edit <code>/home/adminuser/php-playground/config/config.php</code> and add your API key</li>
                <li>Restart the container: <code>docker-compose restart</code></li>
            </ul>
        </div>
    </div>
</body>
</html>

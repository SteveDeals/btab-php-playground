<?php
require_once 'api-helper.php';

// Get API key info
$apiKeyInfo = getApiKeyInfo();
$user = getCurrentUser();

// Test API connection
$testResults = [];

// Test 1: Check if API key is configured
$keyStatusMessage = 'API key is not configured';
if ($apiKeyInfo['has_key']) {
    if ($apiKeyInfo['source'] === 'user') {
        $keyStatusMessage = 'Using your personal API key (logged in as ' . $apiKeyInfo['username'] . ')';
    } else {
        $keyStatusMessage = 'Using default API key from config.php';
    }
}

$testResults[] = [
    'name' => 'API Key Configuration',
    'status' => $apiKeyInfo['has_key'] ? 'pass' : 'fail',
    'message' => $keyStatusMessage
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
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>API Connection Test</h1>
                    <div class="nav">
                        <a href="index.php">← Home</a>
                        <a href="products.php">Products</a>
                    </div>
                </div>
                <div style="font-size: 0.9em;">
                    <?php if ($user): ?>
                        <strong><?php echo htmlspecialchars($user['github_username']); ?></strong> |
                        <a href="/manage-key.php" style="color: #667eea;">Manage Key</a> |
                        <a href="/auth/logout.php" style="color: #666;">Logout</a>
                    <?php else: ?>
                        <a href="/auth/login.php" style="color: #667eea;">Login with GitHub</a>
                    <?php endif; ?>
                </div>
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
                <?php if (!$user): ?>
                    <li><strong>Not logged in?</strong> <a href="/auth/login.php">Login with GitHub</a> to manage your own API key</li>
                <?php endif; ?>
                <?php if ($user && !$apiKeyInfo['has_key']): ?>
                    <li><strong>No API key configured!</strong> <a href="/manage-key.php">Go to Manage Key</a> to add yours</li>
                <?php endif; ?>
                <li>Make sure you've registered at <a href="https://dashboard.btab.app/register" target="_blank">dashboard.btab.app</a></li>
                <li>Copy your API key from the <a href="https://dashboard.btab.app" target="_blank">dashboard</a> after logging in</li>
                <?php if ($user): ?>
                    <li>Add your key at <a href="/manage-key.php">Manage Key</a> - it will be used automatically</li>
                <?php endif; ?>
                <li>Check the <a href="/">homepage</a> for setup instructions</li>
            </ul>
        </div>
    </div>
</body>
</html>

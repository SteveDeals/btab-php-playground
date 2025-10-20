<?php
/**
 * API Key Management Page
 * Allows developers to add/update their Btab API key
 */

require_once 'includes/auth.php';
require_once 'includes/db.php';

// Require authentication
requireAuth();

$user = getCurrentUser();
$username = getCurrentUsername();
$message = null;
$messageType = 'info';

// Check if user has an existing API key
$hasKey = hasDeveloperKey($username);
$developerInfo = getDeveloperInfo($username);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $apiKey = trim($_POST['api_key'] ?? '');

        if (empty($apiKey)) {
            $message = 'Please enter your API key';
            $messageType = 'error';
        } elseif (!preg_match('/^btab_(live|test)_[a-zA-Z0-9]{40,}$/', $apiKey)) {
            $message = 'Invalid API key format. Should start with btab_live_ or btab_test_';
            $messageType = 'error';
        } else {
            // Save the API key
            if (saveDeveloperKey($username, $user['github_id'], $user['email'], $apiKey)) {
                $message = 'API key saved successfully!';
                $messageType = 'success';
                $hasKey = true;
                $developerInfo = getDeveloperInfo($username);
            } else {
                $message = 'Failed to save API key. Please try again.';
                $messageType = 'error';
            }
        }
    } elseif ($action === 'delete') {
        if (deleteDeveloperKey($username)) {
            $message = 'API key deleted successfully';
            $messageType = 'success';
            $hasKey = false;
            $developerInfo = null;
        } else {
            $message = 'Failed to delete API key';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage API Key - Btab PHP Playground</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        h1 {
            color: #667eea;
            font-size: 2em;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .user-info .username {
            font-weight: bold;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        .message.success {
            background: #efe;
            border-color: #3c3;
            color: #060;
        }
        .message.error {
            background: #fee;
            border-color: #c33;
            color: #800;
        }
        .message.info {
            background: #eef;
            border-color: #33c;
            color: #006;
        }
        .form-group {
            margin: 20px 0;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Courier New', monospace;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-group .help-text {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .status-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .status-box .status-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .status-box .status-item:last-child {
            border-bottom: none;
        }
        .status-box .label {
            font-weight: bold;
            color: #555;
        }
        .status-box .value {
            color: #333;
        }
        .nav {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .nav a {
            margin-right: 15px;
            color: #667eea;
            text-decoration: none;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        .instructions {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .instructions h3 {
            margin-bottom: 10px;
            color: #856404;
        }
        .instructions ol {
            margin-left: 20px;
            color: #856404;
        }
        .instructions li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage API Key</h1>
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Avatar">
                <div>
                    <div class="username"><?php echo htmlspecialchars($user['github_username']); ?></div>
                    <a href="/auth/logout.php" style="font-size: 0.9em; color: #666;">Logout</a>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!$hasKey): ?>
            <div class="instructions">
                <h3>Get Your API Key</h3>
                <ol>
                    <li>Go to <a href="https://dashboard.btab.app/register" target="_blank">dashboard.btab.app/register</a> and create your vendor account</li>
                    <li>Login to the dashboard after registration</li>
                    <li>Copy your API key from the dashboard</li>
                    <li>Paste it below and click "Save API Key"</li>
                </ol>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="action" value="save">

            <div class="form-group">
                <label for="api_key">Btab API Key</label>
                <input
                    type="password"
                    id="api_key"
                    name="api_key"
                    placeholder="btab_live_..."
                    <?php echo $hasKey ? '' : 'required'; ?>
                >
                <div class="help-text">
                    Your API key will be encrypted and stored securely. It will only be used to make API calls from your PHP code.
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn">
                    <?php echo $hasKey ? 'Update API Key' : 'Save API Key'; ?>
                </button>
                <?php if ($hasKey): ?>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        Delete API Key
                    </button>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($hasKey && $developerInfo): ?>
            <div class="status-box">
                <h3 style="margin-bottom: 15px; color: #667eea;">API Key Status</h3>
                <div class="status-item">
                    <span class="label">Status:</span>
                    <span class="value" style="color: #28a745; font-weight: bold;">✓ Configured</span>
                </div>
                <div class="status-item">
                    <span class="label">GitHub Username:</span>
                    <span class="value"><?php echo htmlspecialchars($developerInfo['github_username']); ?></span>
                </div>
                <div class="status-item">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo htmlspecialchars($developerInfo['email'] ?? 'N/A'); ?></span>
                </div>
                <div class="status-item">
                    <span class="label">Created:</span>
                    <span class="value"><?php echo date('M j, Y g:i A', strtotime($developerInfo['created_at'])); ?></span>
                </div>
                <div class="status-item">
                    <span class="label">Last Updated:</span>
                    <span class="value"><?php echo date('M j, Y g:i A', strtotime($developerInfo['updated_at'])); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="nav">
            <h3 style="margin-bottom: 10px;">What's Next?</h3>
            <a href="/">← Home</a>
            <a href="/products.php">View Products</a>
            <a href="/test-api.php">Test API Connection</a>
            <a href="https://dashboard.btab.app" target="_blank">Dashboard</a>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 0.9em;">
            <p><strong>Security:</strong> Your API key is encrypted before storage and only decrypted when making API calls. It is never exposed to your browser or in git repositories.</p>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete your API key? You will need to add it again to use the API.')) {
                const form = document.createElement('form');
                form.method = 'post';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'action';
                input.value = 'delete';

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

<?php
/**
 * GitHub OAuth Callback
 * Handles the OAuth callback from GitHub
 */

require_once '../includes/auth.php';

$error = null;

// Check for OAuth error
if (isset($_GET['error'])) {
    $error = 'GitHub authentication failed: ' . htmlspecialchars($_GET['error_description'] ?? 'Unknown error');
}

// Verify we have required parameters
if (!$error && (!isset($_GET['code']) || !isset($_GET['state']))) {
    $error = 'Invalid OAuth callback: missing required parameters';
}

// Verify CSRF state
if (!$error && !verifyState($_GET['state'])) {
    $error = 'Invalid OAuth state: possible CSRF attack';
}

// Exchange code for access token
$accessToken = null;
if (!$error) {
    $accessToken = getGithubAccessToken($_GET['code']);
    if (!$accessToken) {
        $error = 'Failed to get access token from GitHub';
    }
}

// Get user info from GitHub
$githubUser = null;
$email = null;
if (!$error) {
    $githubUser = getGithubUser($accessToken);
    if (!$githubUser) {
        $error = 'Failed to get user information from GitHub';
    } else {
        $email = getGithubUserEmail($accessToken);
    }
}

// Login user
if (!$error) {
    loginUser($githubUser, $email);

    // Redirect to manage key page
    header('Location: /manage-key.php');
    exit;
}

// Show error page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Error - Btab PHP Playground</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #c33;
            margin-bottom: 20px;
        }
        .error {
            background: #fee;
            border-left: 4px solid #c33;
            padding: 15px;
            border-radius: 4px;
            color: #800;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Authentication Error</h1>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

        <a href="/auth/login.php" class="btn">Try Again</a>
        <a href="/" class="btn" style="background: #666;">Go Home</a>
    </div>
</body>
</html>

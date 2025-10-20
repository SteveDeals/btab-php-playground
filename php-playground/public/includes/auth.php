<?php
/**
 * GitHub OAuth Authentication Helper
 * Handles OAuth flow and session management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// GitHub OAuth Configuration (set in config.php or environment)
define('GITHUB_CLIENT_ID', $_ENV['GITHUB_CLIENT_ID'] ?? '');
define('GITHUB_CLIENT_SECRET', $_ENV['GITHUB_CLIENT_SECRET'] ?? '');
define('GITHUB_REDIRECT_URI', $_ENV['GITHUB_REDIRECT_URI'] ?? 'https://playground-php.btab.app/auth/callback.php');

/**
 * Get GitHub OAuth authorization URL
 */
function getGithubAuthUrl() {
    $params = [
        'client_id' => GITHUB_CLIENT_ID,
        'redirect_uri' => GITHUB_REDIRECT_URI,
        'scope' => 'user:email',
        'state' => generateState()
    ];

    return 'https://github.com/login/oauth/authorize?' . http_build_query($params);
}

/**
 * Generate and store CSRF state token
 */
function generateState() {
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth_state'] = $state;
    return $state;
}

/**
 * Verify CSRF state token
 */
function verifyState($state) {
    return isset($_SESSION['oauth_state']) && $_SESSION['oauth_state'] === $state;
}

/**
 * Exchange OAuth code for access token
 */
function getGithubAccessToken($code) {
    $data = [
        'client_id' => GITHUB_CLIENT_ID,
        'client_secret' => GITHUB_CLIENT_SECRET,
        'code' => $code,
        'redirect_uri' => GITHUB_REDIRECT_URI
    ];

    $ch = curl_init('https://github.com/login/oauth/access_token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        return $result['access_token'] ?? null;
    }

    return null;
}

/**
 * Get GitHub user info
 */
function getGithubUser($accessToken) {
    $ch = curl_init('https://api.github.com/user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'User-Agent: Btab-PHP-Playground'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        return json_decode($response, true);
    }

    return null;
}

/**
 * Get GitHub user's primary email
 */
function getGithubUserEmail($accessToken) {
    $ch = curl_init('https://api.github.com/user/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'User-Agent: Btab-PHP-Playground'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $emails = json_decode($response, true);

        // Find primary email
        foreach ($emails as $email) {
            if ($email['primary'] ?? false) {
                return $email['email'];
            }
        }

        // Fallback to first email
        return $emails[0]['email'] ?? null;
    }

    return null;
}

/**
 * Login user (store in session)
 */
function loginUser($githubUser, $email) {
    $_SESSION['user'] = [
        'github_id' => $githubUser['id'],
        'github_username' => $githubUser['login'],
        'avatar_url' => $githubUser['avatar_url'],
        'name' => $githubUser['name'] ?? $githubUser['login'],
        'email' => $email,
        'logged_in_at' => time()
    ];
}

/**
 * Logout user (clear session)
 */
function logoutUser() {
    unset($_SESSION['user']);
    unset($_SESSION['oauth_state']);
    session_destroy();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user']);
}

/**
 * Get current logged in user
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Get current user's GitHub username
 */
function getCurrentUsername() {
    $user = getCurrentUser();
    return $user['github_username'] ?? null;
}

/**
 * Require authentication (redirect to login if not logged in)
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
}

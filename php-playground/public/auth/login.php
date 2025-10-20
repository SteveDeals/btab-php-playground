<?php
/**
 * GitHub OAuth Login
 * Redirects user to GitHub for authentication
 */

require_once '../includes/auth.php';

// If already logged in, redirect to manage key page
if (isLoggedIn()) {
    header('Location: /manage-key.php');
    exit;
}

// Get GitHub auth URL and redirect
$authUrl = getGithubAuthUrl();
header('Location: ' . $authUrl);
exit;

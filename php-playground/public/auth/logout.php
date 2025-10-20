<?php
/**
 * Logout
 * Clear session and redirect to home
 */

require_once '../includes/auth.php';

logoutUser();

header('Location: /?logged_out=1');
exit;

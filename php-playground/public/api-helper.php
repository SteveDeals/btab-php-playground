<?php
require_once '/var/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

/**
 * Get API key for current context
 * Returns user-specific key if logged in, otherwise falls back to default config key
 */
function getActiveApiKey() {
    // Check if user is logged in
    if (isLoggedIn()) {
        $username = getCurrentUsername();
        $userKey = getDeveloperKey($username);

        if ($userKey) {
            return $userKey;
        }
    }

    // Fall back to default config key
    return defined('BTAB_API_KEY') ? BTAB_API_KEY : '';
}

/**
 * Make API request to Btab API
 */
function btabApiCall($endpoint, $method = 'GET', $data = null) {
    $apiKey = getActiveApiKey();

    if (empty($apiKey)) {
        return [
            'error' => 'No API key configured. Please login and add your API key.',
            'http_code' => 0
        ];
    }

    $url = BTAB_API_URL . '/' . ltrim($endpoint, '/');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['error' => 'cURL Error: ' . $error, 'http_code' => 0];
    }

    $decoded = json_decode($response, true);
    $decoded['http_code'] = $httpCode;

    return $decoded;
}

/**
 * Get vendor's products
 */
function getMyProducts() {
    return btabApiCall('my-products');
}

/**
 * Get all products from catalog
 */
function getAllProducts($params = []) {
    $query = http_build_query($params);
    $endpoint = 'products' . ($query ? '?' . $query : '');
    return btabApiCall($endpoint);
}

/**
 * Create an order
 */
function createOrder($orderData) {
    return btabApiCall('orders', 'POST', $orderData);
}

/**
 * Format price in cents to dollars
 */
function formatPrice($cents) {
    return '$' . number_format($cents / 100, 2);
}

/**
 * Check if API key is configured
 * Checks for user-specific key or default config key
 */
function isApiKeyConfigured() {
    $apiKey = getActiveApiKey();
    return !empty($apiKey);
}

/**
 * Get information about which API key is being used
 */
function getApiKeyInfo() {
    $info = [
        'has_key' => false,
        'source' => 'none',
        'username' => null
    ];

    if (isLoggedIn()) {
        $username = getCurrentUsername();
        $userKey = getDeveloperKey($username);

        if ($userKey) {
            $info['has_key'] = true;
            $info['source'] = 'user';
            $info['username'] = $username;
            return $info;
        }
    }

    if (defined('BTAB_API_KEY') && !empty(BTAB_API_KEY)) {
        $info['has_key'] = true;
        $info['source'] = 'config';
    }

    return $info;
}

<?php
require_once '/var/config/config.php';

/**
 * Make API request to Btab API
 */
function btabApiCall($endpoint, $method = 'GET', $data = null) {
    $url = BTAB_API_URL . '/' . ltrim($endpoint, '/');
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . BTAB_API_KEY,
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
 */
function isApiKeyConfigured() {
    return !empty(BTAB_API_KEY);
}

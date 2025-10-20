<?php
/**
 * Database Helper for Developer API Keys
 * Handles SQLite storage and encryption of developer API keys
 */

// Database file location (persisted in Docker volume)
define('DB_FILE', '/var/lib/playground/developer_keys.db');

// Encryption key (should be set in config.php)
define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY'] ?? 'change-this-to-random-32-char-key');
define('ENCRYPTION_METHOD', 'AES-256-CBC');

/**
 * Get database connection
 */
function getDb() {
    try {
        $db = new PDO('sqlite:' . DB_FILE);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create tables if they don't exist
        createTables($db);

        return $db;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}

/**
 * Create database tables
 */
function createTables($db) {
    $sql = "CREATE TABLE IF NOT EXISTS developer_keys (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        github_username TEXT UNIQUE NOT NULL,
        github_id INTEGER UNIQUE NOT NULL,
        email TEXT,
        api_key_encrypted TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    try {
        $db->exec($sql);
    } catch (PDOException $e) {
        error_log("Failed to create tables: " . $e->getMessage());
    }
}

/**
 * Encrypt API key
 */
function encryptApiKey($apiKey) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD));
    $encrypted = openssl_encrypt($apiKey, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);

    // Combine IV and encrypted data
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt API key
 */
function decryptApiKey($encryptedData) {
    $data = base64_decode($encryptedData);
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);

    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);

    return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}

/**
 * Save or update developer's API key
 */
function saveDeveloperKey($githubUsername, $githubId, $email, $apiKey) {
    $db = getDb();
    if (!$db) return false;

    try {
        $encryptedKey = encryptApiKey($apiKey);

        $stmt = $db->prepare("
            INSERT INTO developer_keys (github_username, github_id, email, api_key_encrypted, updated_at)
            VALUES (:username, :github_id, :email, :key, CURRENT_TIMESTAMP)
            ON CONFLICT(github_username)
            DO UPDATE SET
                api_key_encrypted = :key,
                email = :email,
                updated_at = CURRENT_TIMESTAMP
        ");

        $stmt->execute([
            ':username' => $githubUsername,
            ':github_id' => $githubId,
            ':email' => $email,
            ':key' => $encryptedKey
        ]);

        return true;
    } catch (PDOException $e) {
        error_log("Failed to save developer key: " . $e->getMessage());
        return false;
    }
}

/**
 * Get developer's API key by GitHub username
 */
function getDeveloperKey($githubUsername) {
    $db = getDb();
    if (!$db) return null;

    try {
        $stmt = $db->prepare("
            SELECT api_key_encrypted
            FROM developer_keys
            WHERE github_username = :username
        ");

        $stmt->execute([':username' => $githubUsername]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return decryptApiKey($result['api_key_encrypted']);
        }

        return null;
    } catch (PDOException $e) {
        error_log("Failed to get developer key: " . $e->getMessage());
        return null;
    }
}

/**
 * Get developer info by GitHub username
 */
function getDeveloperInfo($githubUsername) {
    $db = getDb();
    if (!$db) return null;

    try {
        $stmt = $db->prepare("
            SELECT github_username, github_id, email, created_at, updated_at
            FROM developer_keys
            WHERE github_username = :username
        ");

        $stmt->execute([':username' => $githubUsername]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to get developer info: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if developer has an API key configured
 */
function hasDeveloperKey($githubUsername) {
    $db = getDb();
    if (!$db) return false;

    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM developer_keys
            WHERE github_username = :username
        ");

        $stmt->execute([':username' => $githubUsername]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    } catch (PDOException $e) {
        error_log("Failed to check developer key: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete developer's API key
 */
function deleteDeveloperKey($githubUsername) {
    $db = getDb();
    if (!$db) return false;

    try {
        $stmt = $db->prepare("
            DELETE FROM developer_keys
            WHERE github_username = :username
        ");

        $stmt->execute([':username' => $githubUsername]);
        return true;
    } catch (PDOException $e) {
        error_log("Failed to delete developer key: " . $e->getMessage());
        return false;
    }
}

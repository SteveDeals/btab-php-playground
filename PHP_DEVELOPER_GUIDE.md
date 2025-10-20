# PHP Developer Guide for Btab API

## Why PHP is the Best Choice

With PHP, your API key stays on the server - much more secure than JavaScript!

```
✅ Secure Flow:
Customer Browser → Your PHP Server (API key here) → Btab API

❌ Risky Flow:
Customer Browser (API key exposed!) → Btab API
```

---

## Quick Start - PHP Examples

### 1. Setup Config File (Keep this PRIVATE)

**config/config.php** (Outside public directory!)
```php
<?php
// NEVER put this in public_html or www directory!
define('BTAB_API_KEY', 'btab_live_your_key_here');
define('BTAB_API_URL', 'https://api.btab.app/api/v1');
?>
```

### 2. Get Products

**public/products.php**
```php
<?php
require_once '../config/config.php';

function getVendorProducts() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, BTAB_API_URL . '/my-products');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . BTAB_API_KEY
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        return json_decode($response, true);
    }
    return null;
}

// Get and display products
$data = getVendorProducts();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Our Products</title>
</head>
<body>
    <h1>Our Store</h1>

    <div class="products">
        <?php if ($data && isset($data['products'])): ?>
            <?php foreach ($data['products'] as $product): ?>
                <div class="product-card">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="price">
                        $<?php echo number_format($product['retail_price_cents'] / 100, 2); ?>
                    </p>
                    <form method="post" action="add-to-cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $product['retail_price_cents']; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
```

### 3. Shopping Cart (Using PHP Sessions)

**public/add-to-cart.php**
```php
<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $price = $_POST['price'];

    // Check if already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId) {
            $item['quantity']++;
            $found = true;
            break;
        }
    }

    // Add new item if not found
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $productId,
            'name' => $productName,
            'price' => $price,
            'quantity' => 1
        ];
    }
}

header('Location: cart.php');
?>
```

**public/cart.php**
```php
<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
</head>
<body>
    <h1>Your Cart</h1>

    <?php if (empty($cart)): ?>
        <p>Your cart is empty</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
            <?php foreach ($cart as $item): ?>
                <?php
                    $itemTotal = ($item['price'] * $item['quantity']) / 100;
                    $total += $itemTotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'] / 100, 2); ?></td>
                    <td>$<?php echo number_format($itemTotal, 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total:</strong></td>
                <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
            </tr>
        </table>

        <a href="checkout.php">Proceed to Checkout</a>
    <?php endif; ?>
</body>
</html>
```

### 4. Create Order

**public/checkout.php**
```php
<?php
session_start();
require_once '../config/config.php';

$cart = $_SESSION['cart'] ?? [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare order data
    $orderData = [
        'items' => array_map(function($item) {
            return [
                'product_id' => $item['id'],
                'quantity' => $item['quantity']
            ];
        }, $cart),
        'customer' => [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'address' => [
                'street' => $_POST['street'],
                'city' => $_POST['city'],
                'state' => $_POST['state'],
                'zip' => $_POST['zip'],
                'country' => $_POST['country'] ?? 'US'
            ]
        ]
    ];

    // Send to API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, BTAB_API_URL . '/orders');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . BTAB_API_KEY,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 201) {
        $result = json_decode($response, true);
        $_SESSION['cart'] = []; // Clear cart
        $message = "Order placed successfully! Order #" . $result['order']['order_number'];
    } else {
        $error = json_decode($response, true);
        $message = "Error: " . ($error['message'] ?? 'Failed to create order');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout</h1>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (!empty($cart)): ?>
        <form method="post">
            <h2>Customer Information</h2>

            <label>Name: <input type="text" name="name" required></label><br>
            <label>Email: <input type="email" name="email" required></label><br>
            <label>Phone: <input type="tel" name="phone"></label><br>

            <h2>Shipping Address</h2>

            <label>Street: <input type="text" name="street" required></label><br>
            <label>City: <input type="text" name="city" required></label><br>
            <label>State: <input type="text" name="state" required></label><br>
            <label>ZIP: <input type="text" name="zip" required></label><br>
            <label>Country: <input type="text" name="country" value="US"></label><br>

            <button type="submit">Place Order</button>
        </form>
    <?php else: ?>
        <p>Your cart is empty. <a href="products.php">Continue shopping</a></p>
    <?php endif; ?>
</body>
</html>
```

### 5. API Proxy (For AJAX Calls)

**public/api-proxy.php**
```php
<?php
require_once '../config/config.php';

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Get request details
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Prevent access to sensitive endpoints
$allowedEndpoints = ['products', 'my-products', 'orders', 'vendor/me'];
$requestedEndpoint = explode('/', $endpoint)[0];

if (!in_array($requestedEndpoint, $allowedEndpoints)) {
    http_response_code(403);
    echo json_encode(['error' => 'Endpoint not allowed']);
    exit;
}

// Build full URL
$url = BTAB_API_URL . '/' . $endpoint;

// Setup cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . BTAB_API_KEY,
    'Content-Type: application/json'
]);

// Handle different HTTP methods
switch ($method) {
    case 'POST':
        curl_setopt($ch, CURLOPT_POST, true);
        $input = file_get_contents('php://input');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        break;
    case 'PUT':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        $input = file_get_contents('php://input');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        break;
    case 'DELETE':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        break;
}

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Return response
http_response_code($httpCode);
echo $response;
?>
```

Now JavaScript can safely call your PHP proxy:
```javascript
// Safe! API key is on PHP server, not in browser
fetch('/api-proxy.php?endpoint=my-products')
    .then(res => res.json())
    .then(data => {
        console.log('Products:', data);
    });
```

---

## Directory Structure

```
/var/www/php-site/
├── public/              # Web root (Apache/Nginx points here)
│   ├── index.php
│   ├── products.php
│   ├── cart.php
│   ├── checkout.php
│   ├── api-proxy.php   # AJAX endpoint
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── config/             # OUTSIDE web root - private!
│   └── config.php      # API key here
├── includes/           # PHP includes
│   ├── header.php
│   └── footer.php
└── .htaccess          # Protect private directories
```

**Important:** Only `public/` directory should be accessible from web!

---

## VPS Setup Options

### Option 1: Use Existing VPS (Simplest)

```bash
# Install PHP
sudo apt update
sudo apt install php8.1 php8.1-curl php8.1-json php8.1-mbstring

# Install Apache
sudo apt install apache2 libapache2-mod-php8.1

# Create directory
sudo mkdir -p /var/www/php-frontend/public
sudo mkdir -p /var/www/php-frontend/config

# Set permissions
sudo chown -R www-data:www-data /var/www/php-frontend

# Configure Apache
sudo nano /etc/apache2/sites-available/php-frontend.conf
```

**Apache Config:**
```apache
<VirtualHost *:80>
    ServerName php-frontend.btab.app
    DocumentRoot /var/www/php-frontend/public

    <Directory /var/www/php-frontend/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Block access to config directory
    <Directory /var/www/php-frontend/config>
        Require all denied
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/php-frontend-error.log
    CustomLog ${APACHE_LOG_DIR}/php-frontend-access.log combined
</VirtualHost>
```

```bash
# Enable site
sudo a2ensite php-frontend
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### Option 2: PHP with Docker (If you prefer)

**docker-compose.yml:**
```yaml
version: '3'
services:
  php-frontend:
    image: php:8.1-apache
    container_name: php-frontend
    volumes:
      - ./php-site:/var/www/html
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.php-frontend.rule=Host(`php.btab.app`)"
    environment:
      - BTAB_API_KEY=${BTAB_API_KEY}
```

### Option 3: Let Developer Use Their Own Hosting

They can use:
- Any shared hosting (GoDaddy, Bluehost, etc.)
- Their own VPS
- Local XAMPP/WAMP for development

Just give them:
1. API endpoint: `https://api.btab.app/api/v1`
2. Test API key
3. Code examples above

---

## Test API Credentials

Create a test vendor account:
```
Email: php-developer@test.com
Password: PhpTest123
Company: PHP Test Store
```

Or use existing demo account:
```
API Key: btab_live_e3aca198eb1e0d9911a0c322cc792315e3dd0ae5fb607ab535e9466f044dee4e
```

---

## Security Best Practices

### DO ✅
- Keep `config.php` OUTSIDE web root
- Use `htmlspecialchars()` for all output
- Validate all user input
- Use prepared statements if using database
- Keep API key in environment variable or config file

### DON'T ❌
- Put API key in JavaScript
- Put config files in public directory
- Trust user input without validation
- Echo API errors directly to user
- Store sensitive data in cookies

---

## Testing Your PHP Site

### 1. Test Products Loading
```bash
curl http://php-frontend.btab.app/products.php
```

### 2. Test API Proxy
```bash
curl http://php-frontend.btab.app/api-proxy.php?endpoint=my-products
```

### 3. Check PHP Errors
```bash
tail -f /var/log/apache2/error.log
```

---

## Common Issues & Solutions

### cURL not working?
```bash
sudo apt install php8.1-curl
sudo systemctl restart apache2
```

### Sessions not working?
```php
// Make sure session_start() is at the very top
<?php
session_start();
// Rest of code...
?>
```

### CORS errors with AJAX?
Add to PHP:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
```

### Can't write to directories?
```bash
sudo chown -R www-data:www-data /var/www/php-frontend
sudo chmod -R 755 /var/www/php-frontend
```

---

## Summary

With PHP, your frontend developer can:
1. Build a complete store with server-side rendering
2. Keep API keys secure on the server
3. Use familiar PHP patterns and hosting
4. Handle payments and sensitive data safely
5. Work with any PHP framework (Laravel, Symfony, etc.)

The API key NEVER goes to the browser - much more secure than JavaScript!
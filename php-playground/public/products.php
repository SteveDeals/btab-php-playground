<?php
require_once 'api-helper.php';

$data = getMyProducts();
$products = $data['products'] ?? [];
$error = $data['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Btab PHP Playground</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
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
        .error {
            background: #fee;
            padding: 15px;
            border-radius: 8px;
            color: #c00;
            margin: 20px 0;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .product-name {
            color: #333;
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .product-price {
            color: #667eea;
            font-size: 1.5em;
            font-weight: bold;
            margin: 10px 0;
        }
        .product-stock {
            color: #666;
            font-size: 0.9em;
        }
        .in-stock { color: #28a745; }
        .out-of-stock { color: #dc3545; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>My Products</h1>
            <div class="nav">
                <a href="index.php">← Home</a>
                <a href="test-api.php">Test API</a>
            </div>
        </header>

        <?php if ($error): ?>
            <div class="error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif (empty($products)): ?>
            <div class="error">
                <strong>No products found!</strong><br>
                Go to <a href="https://dashboard.btab.app" target="_blank">dashboard.btab.app</a> and add some products to your store.
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                        
                        <div class="product-price">
                            <?php
                            // Use custom price if available, otherwise use retail price
                            $price = $product['custom_retail_price_cents'] ?? $product['retail_price_cents'] ?? 0;
                            echo formatPrice($price);
                            ?>
                        </div>
                        
                        <p class="product-stock">
                            <?php if ($product['in_stock']): ?>
                                <span class="in-stock">✓ In Stock (<?php echo $product['stock_quantity']; ?>)</span>
                            <?php else: ?>
                                <span class="out-of-stock">✗ Out of Stock</span>
                            <?php endif; ?>
                        </p>
                        
                        <button class="btn">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

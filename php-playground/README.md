# Btab PHP Playground

Welcome to your PHP development environment for building with the Btab API!

## Quick Start

### 1. Access Your Development Site
Your PHP playground is live at: **https://playground-php.btab.app**

### 2. Get Your API Key

1. Register for a vendor account: [https://dashboard.btab.app/register](https://dashboard.btab.app/register)
2. Login to the dashboard after registration
3. Copy your API key from the dashboard

### 3. Configure Your API Key

Edit the config file on the VPS:

```bash
# SSH to VPS
ssh btab

# Edit config
nano /home/adminuser/php-playground/config/config.php

# Change this line:
define('BTAB_API_KEY', 'your_api_key_here');

# Save and exit (Ctrl+X, then Y, then Enter)

# Restart container
cd /home/adminuser/php-playground
docker compose restart
```

### 4. Start Building!

Your files go in: `/home/adminuser/php-playground/public/`

Upload via SFTP or edit directly on the VPS.

## Example Pages

Once configured, check out these examples:

- **Home**: https://playground-php.btab.app
- **Products**: https://playground-php.btab.app/products.php
- **API Test**: https://playground-php.btab.app/test-api.php

## API Helper Functions

The `api-helper.php` file provides these functions:

```php
// Get your vendor's products
$data = getMyProducts();

// Get all products from catalog
$products = getAllProducts(['in_stock' => 'true']);

// Create an order
$order = createOrder($orderData);

// Format price
echo formatPrice(1999); // Outputs: $19.99
```

## Directory Structure

```
/home/adminuser/php-playground/
├── public/              # Your PHP files (web-accessible)
│   ├── index.php
│   ├── products.php
│   ├── test-api.php
│   ├── api-helper.php
│   └── health.php
├── config/             # Configuration (PRIVATE - not web-accessible)
│   └── config.php      # Put your API key here
├── Dockerfile
├── docker-compose.yml
└── README.md           # This file
```

## API Endpoints

- **Base URL**: https://api.btab.app/api/v1
- **Auth**: Bearer token (your API key)

### Common Endpoints:

- `GET /my-products` - Get your vendor's products
- `GET /products` - Browse full product catalog
- `POST /orders` - Create customer order
- `GET /orders` - List your orders
- `GET /vendor/me` - Get your vendor profile

## Working with Files

### Option 1: SFTP Upload

Use any SFTP client (FileZilla, Cyberduck, etc.):

- **Host**: your VPS IP or domain
- **Username**: adminuser
- **Path**: `/home/adminuser/php-playground/public/`

### Option 2: Direct Edit on VPS

```bash
ssh btab
cd /home/adminuser/php-playground/public
nano myfile.php
```

### Option 3: Git Clone

```bash
ssh btab
cd /home/adminuser/php-playground/public
git clone your-repo.git .
```

## Container Management

```bash
# Restart container (after config changes)
cd /home/adminuser/php-playground
docker compose restart

# View logs
docker compose logs -f

# Rebuild container (after Dockerfile changes)
docker compose up -d --build

# Stop container
docker compose down

# Start container
docker compose up -d
```

## Important Security Notes

✅ **DO:**
- Keep API key in `config/config.php` (outside public directory)
- Use HTTPS (already configured)
- Validate all user input
- Use `htmlspecialchars()` for output

❌ **DON'T:**
- Put API keys in JavaScript or HTML
- Store sensitive data in cookies
- Trust user input without validation

## Resources

- **API Documentation**: See PHP_DEVELOPER_GUIDE.md
- **Dashboard**: https://dashboard.btab.app
- **Production API**: https://api.btab.app/api/v1
- **Your Playground**: https://playground-php.btab.app

## Troubleshooting

### API key not working?

1. Check it's correctly set in `/home/adminuser/php-playground/config/config.php`
2. Restart container: `docker compose restart`
3. Test connection at: https://playground-php.btab.app/test-api.php

### Page not loading?

```bash
# Check if container is running
docker ps | grep php-playground

# View logs
cd /home/adminuser/php-playground
docker compose logs -f
```

### CORS errors?

The API proxy pattern in `api-helper.php` handles this for you.

## Need Help?

- Check example files in `/home/adminuser/php-playground/public/`
- Read the PHP Developer Guide
- Test API connection at /test-api.php

Happy coding! 🚀

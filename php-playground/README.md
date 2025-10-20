# Btab PHP Playground

Welcome to your PHP development environment for building with the Btab API!

## Quick Start

### 1. Access Your Development Site
Your PHP playground is live at: **https://playground-php.btab.app**

### 2. Get Your API Key

1. Register for a vendor account: [https://dashboard.btab.app/register](https://dashboard.btab.app/register)
2. Login to the dashboard after registration
3. Copy your API key from the dashboard
4. Contact admin to add your API key to the server

### 3. Start Building!

1. Clone this repository
2. Edit files in `public/` directory
3. Commit and push to GitHub
4. Changes auto-deploy to https://playground-php.btab.app in ~30 seconds!

```bash
# Make changes
nano public/index.php

# Commit and push
git add .
git commit -m "Update homepage"
git push origin master

# Auto-deploys!
```

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
php-playground/
├── public/              # Your PHP files (web-accessible)
│   ├── index.php
│   ├── products.php
│   ├── test-api.php
│   ├── api-helper.php
│   └── health.php
├── config/             # Configuration (PRIVATE - not web-accessible)
│   ├── config.example.php  # Template (in git)
│   └── config.php          # Your API key (NOT in git)
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

## Development Workflow

### Edit Locally

1. Clone the repository
2. Edit files in your favorite editor
3. Test locally with Docker (optional)
4. Push to GitHub

### Local Testing (Optional)

```bash
# Copy config template
cp config/config.example.php config/config.php

# Add your API key to config.php

# Run with Docker
docker compose up -d

# Visit http://localhost
```

### Deploy to Production

Simply push to GitHub and it auto-deploys:

```bash
git add .
git commit -m "Your changes"
git push origin master
# Wait ~30 seconds - live at playground-php.btab.app!
```

### Check Deployment Status

- Go to GitHub Actions tab in the repository
- Watch the "Deploy PHP Playground to VPS" workflow
- Green checkmark = deployed successfully!

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

Visit https://playground-php.btab.app/test-api.php to check API connection status. If it shows "API key not configured", contact admin to add your key to the server.

### Changes not showing up?

1. Check GitHub Actions tab for deployment status
2. Wait ~30 seconds after pushing
3. Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)
4. View deployment logs in GitHub Actions

### Page not loading?

- Check if deployment succeeded in GitHub Actions
- Try `/test-api.php` to verify the site is up
- Contact admin if issue persists

### CORS errors?

The API proxy pattern in `api-helper.php` handles this for you automatically.

## Need Help?

- **API Documentation**: See `../PHP_DEVELOPER_GUIDE.md` in repo root
- **Test API**: https://playground-php.btab.app/test-api.php
- **Dashboard**: https://dashboard.btab.app
- **Example Files**: Check `public/` directory for working examples

Happy coding! 🚀

# Btab PHP Playground

A PHP development environment for building customer-facing storefronts with the Btab fulfillment API.

## Live Site

🌐 **https://playground-php.btab.app**

## Quick Start for Developers

### 1. Get Your API Key

1. Register at [dashboard.btab.app/register](https://dashboard.btab.app/register)
2. Login and copy your API key from the dashboard

### 2. Set Up Locally (Optional)

If you want to develop locally before deploying:

```bash
# Clone this repository
git clone <your-repo-url>
cd btab-frontend-playground

# Navigate to PHP playground
cd php-playground

# Copy config example
cp config/config.example.php config/config.php

# Edit config.php and add your API key
nano config/config.php

# Run with Docker
docker compose up -d

# Visit http://localhost
```

### 3. Deploy to Production

**This repo has auto-deploy enabled!**

Simply push to the `main` or `master` branch and GitHub Actions will automatically deploy to the VPS:

```bash
# Make your changes
nano php-playground/public/index.php

# Commit and push
git add .
git commit -m "Update homepage"
git push origin main

# Deployment happens automatically!
```

## Project Structure

```
btab-frontend-playground/
├── php-playground/              # Main PHP application
│   ├── public/                  # Web-accessible PHP files
│   │   ├── index.php           # Landing page
│   │   ├── products.php        # Product listing example
│   │   ├── test-api.php        # API connection test
│   │   ├── api-helper.php      # Reusable API functions
│   │   └── health.php          # Docker health check
│   ├── config/                  # Configuration (NOT web-accessible)
│   │   ├── config.example.php  # Template (tracked in git)
│   │   └── config.php          # Your API key (gitignored!)
│   ├── Dockerfile              # PHP container definition
│   ├── docker-compose.yml      # Container orchestration
│   └── README.md               # Detailed playground docs
├── PHP_DEVELOPER_GUIDE.md      # Complete API integration guide
├── .github/workflows/
│   └── deploy.yml              # Auto-deployment workflow
└── README.md                   # This file
```

## How It Works

1. **You develop** - Edit files in `php-playground/public/`
2. **You push** - `git push origin main`
3. **GitHub Actions** - Auto-deploys to VPS
4. **Docker restarts** - PHP container picks up changes
5. **Live in seconds** - Changes appear at playground-php.btab.app

## Security

✅ **API keys are safe:**
- `config.php` is excluded from git via `.gitignore`
- Only `config.example.php` is tracked
- API key stays server-side (never in browser JavaScript)

✅ **Read-only config mount:**
- Config directory is mounted as `:ro` (read-only) in Docker

✅ **HTTPS everywhere:**
- Traefik provides automatic SSL via Let's Encrypt

## API Documentation

See **[PHP_DEVELOPER_GUIDE.md](./PHP_DEVELOPER_GUIDE.md)** for:
- Complete API integration examples
- Cart and checkout implementations
- Error handling patterns
- Security best practices

## Environment

- **PHP**: 8.1 with Apache
- **API**: https://api.btab.app/api/v1
- **Dashboard**: https://dashboard.btab.app
- **Documentation**: Btab API uses Bearer token authentication

## Troubleshooting

### API key not working?
Contact the admin to verify your API key is configured on the server.

### Changes not showing up?
1. Check GitHub Actions tab for deployment status
2. Wait ~30 seconds for deployment to complete
3. Hard refresh your browser (Ctrl+Shift+R)

### Need help?
- **API Questions**: See [PHP_DEVELOPER_GUIDE.md](./PHP_DEVELOPER_GUIDE.md)
- **Dashboard Issues**: https://dashboard.btab.app
- **Deployment Status**: Check the Actions tab in GitHub

## Resources

- **Live Site**: https://playground-php.btab.app
- **API Documentation**: See PHP_DEVELOPER_GUIDE.md
- **Example Pages**: /products.php, /test-api.php
- **Register for API Key**: https://dashboard.btab.app/register

## License

This is a development playground. Build whatever you want!
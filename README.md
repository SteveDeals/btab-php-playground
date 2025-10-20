# Btab PHP Playground

A PHP development environment for building customer-facing storefronts with the Btab fulfillment API.

## Live Site

🌐 **https://playground-php.btab.app**

## Quick Start for Developers

### 1. Login and Add Your API Key

**NEW: Self-Service API Key Management!**

1. Visit [playground-php.btab.app](https://playground-php.btab.app)
2. Click "Login with GitHub" to authenticate
3. Register for a Btab vendor account at [dashboard.btab.app/register](https://dashboard.btab.app/register)
4. Copy your API key from the dashboard
5. Go to "Manage Key" in the playground and paste your key
6. Start building - all API calls automatically use your key!

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

## Features

✨ **Self-Service API Key Management**
- Developers login with GitHub OAuth
- Add/update their own Btab API keys
- No admin intervention needed
- Scales to 200+ developers

🔐 **Secure Key Storage**
- API keys encrypted before storage
- Stored in SQLite database
- Automatic key selection per developer
- Falls back to default config key if not logged in

⚡ **Auto-Deploy**
- Push to GitHub → Changes live in ~30 seconds
- No manual deployment needed

## Security

✅ **API keys are safe:**
- Individual developer keys encrypted in database
- `config.php` is excluded from git via `.gitignore`
- Only `config.example.php` is tracked
- API keys stay server-side (never in browser JavaScript)

✅ **GitHub OAuth:**
- Secure authentication via GitHub
- CSRF protection with state tokens
- Session-based user management

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

## Admin Setup

For administrators setting up the OAuth integration, see **[OAUTH_SETUP.md](./OAUTH_SETUP.md)**:
- How to create GitHub OAuth app
- Environment variable configuration
- Database setup
- Troubleshooting guide

## Troubleshooting

### API key not working?
1. Make sure you're logged in with GitHub
2. Go to "Manage Key" and verify your key is saved
3. Check [playground-php.btab.app/test-api.php](https://playground-php.btab.app/test-api.php) for API connection status

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
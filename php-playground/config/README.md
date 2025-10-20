# Configuration Directory

## Setup Instructions

1. Copy `config.example.php` to `config.php`:
   ```bash
   cp config.example.php config.php
   ```

2. Edit `config.php` and add your Btab API key:
   ```php
   define('BTAB_API_KEY', 'your_api_key_here');
   ```

3. Get your API key from: https://dashboard.btab.app

## Important Security Notes

- ⚠️ **NEVER** commit `config.php` to git!
- ✅ The `.gitignore` file excludes `config.php` automatically
- ✅ Only `config.example.php` is tracked in version control
- ✅ The `config/` directory is mounted as read-only in the Docker container

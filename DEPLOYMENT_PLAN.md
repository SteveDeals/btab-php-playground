# Self-Service API Key Management - Deployment Plan

## Current Status

✅ All changes committed to feature branch: `feature/self-service-api-keys`
✅ Master branch unchanged - safe to revert
🔗 GitHub: https://github.com/SteveDeals/btab-php-playground

## Testing the Feature (Without Affecting Production)

### Option 1: Test on VPS (Recommended)

1. **SSH to VPS and checkout feature branch:**
   ```bash
   ssh btab
   cd /home/adminuser/php-playground
   git fetch origin
   git checkout feature/self-service-api-keys
   ```

2. **Set up OAuth (one-time):**
   - Follow `OAUTH_SETUP.md` to create GitHub OAuth app
   - Create `.env` file in `php-playground/` subdirectory
   - Add OAuth credentials and encryption key

3. **Deploy feature branch:**
   ```bash
   cd php-playground
   docker compose down
   docker compose up -d --build
   ```

4. **Test the flow:**
   - Visit https://playground-php.btab.app
   - Click "Login with GitHub"
   - Go to "Manage Key" and add a test API key
   - Verify it works on /test-api.php

### Option 2: Test Locally

1. **Clone and checkout feature branch:**
   ```bash
   git clone https://github.com/SteveDeals/btab-php-playground.git
   cd btab-php-playground
   git checkout feature/self-service-api-keys
   ```

2. **Set up local environment:**
   ```bash
   cd php-playground
   cp .env.example .env
   # Edit .env with your GitHub OAuth app (create one for localhost)
   # Callback URL: http://localhost/auth/callback.php
   ```

3. **Run with Docker:**
   ```bash
   docker compose up -d --build
   # Visit http://localhost
   ```

## Deploying to Production

If testing looks good, merge to master:

```bash
# Switch to master branch
git checkout master

# Merge feature branch
git merge feature/self-service-api-keys

# Push to GitHub (triggers auto-deploy)
git push origin master
```

**Note:** Make sure OAuth is configured on VPS before merging!

## Reverting If Needed

### Quick Revert (Go Back to Current Version)

**On VPS:**
```bash
ssh btab
cd /home/adminuser/php-playground
git checkout master
cd php-playground
docker compose down
docker compose up -d --build
```

This returns to the previous working version immediately.

### If Already Merged to Master

**Option 1: Revert the commit**
```bash
git revert <commit-hash>
git push origin master
```

**Option 2: Reset to previous commit (dangerous!)**
```bash
# Find the commit before the merge
git log --oneline

# Reset to that commit
git reset --hard <previous-commit-hash>
git push --force origin master
```

**Option 3: Create a new branch from old master**
```bash
# Before merging, create a backup branch
git checkout master
git checkout -b backup-before-oauth
git push origin backup-before-oauth

# Then if you need to revert:
git checkout backup-before-oauth
git checkout -b master-restored
git push origin master-restored

# On VPS, checkout the restored branch
cd /home/adminuser/php-playground
git fetch origin
git checkout master-restored
cd php-playground
docker compose restart
```

## Auto-Deploy Behavior

**Current setup:**
- GitHub Actions workflow deploys on push to `master` branch
- Feature branch won't auto-deploy (safe to test manually on VPS)

**To deploy feature branch automatically:**
Edit `.github/workflows/deploy.yml`:
```yaml
on:
  push:
    branches: [ main, master, feature/self-service-api-keys ]  # Add feature branch
```

## Rollback Checklist

If you need to rollback:

- [ ] Stop the PHP container: `docker compose down`
- [ ] Switch back to master branch: `git checkout master`
- [ ] Rebuild container: `docker compose up -d --build`
- [ ] Verify site is accessible
- [ ] Check that old workflow still works

## What Gets Reverted

When you switch back to master:

✅ All code changes revert
✅ Docker configuration reverts
✅ API helper reverts to config-only keys
❌ Database file persists (but won't be used)
❌ GitHub OAuth app still exists (can be deleted)
❌ Environment variables still in `.env` (harmless)

## Branches Overview

- **master** - Current production version (working)
- **feature/self-service-api-keys** - New OAuth system (testing)
- **backup-before-oauth** - (optional) Safety backup

## Testing Checklist

Before merging to master, verify:

- [ ] GitHub OAuth login works
- [ ] Can add API key via "Manage Key"
- [ ] API calls use the new key automatically
- [ ] Test page shows correct key source
- [ ] Products page works with user key
- [ ] Logout clears session properly
- [ ] Non-logged-in users see old behavior
- [ ] Database persists across container restarts
- [ ] Auto-deploy still works on feature branch

## Support

If you run into issues:

1. Check `OAUTH_SETUP.md` troubleshooting section
2. View container logs: `docker compose logs -f`
3. Check database permissions: `docker exec btab-php-playground ls -la /var/lib/playground`
4. Test OAuth flow manually
5. Revert to master if blocking

## Decision Points

**Keep the feature if:**
- OAuth flow works smoothly
- Developers can manage keys easily
- No performance issues
- Database scaling is acceptable

**Revert if:**
- OAuth is too complex for developers
- Security concerns arise
- Performance degrades
- Database issues occur
- Simpler solution is preferred

## Next Steps

1. ✅ Test feature branch on VPS or locally
2. ⏳ Verify OAuth flow with real developer
3. ⏳ Check all example pages work correctly
4. ⏳ Decide: merge to master or revert
5. ⏳ Update documentation based on decision

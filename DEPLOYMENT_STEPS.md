# Production Deployment Steps

## Overview
This document outlines the steps to deploy changes to production.

## Files Changed (Latest Deployment)
- Password reset system fixes (business & athlete)
- Dark mode removal (athlete auth pages)
- Tailwind config (dark mode disabled)
- Email system improvements

## Deployment Steps

### 1. Commit and Push Changes to Git

```bash
# Check what files have changed
git status

# Add all changed files
git add .

# Commit the changes
git commit -m "Fix password reset system and remove dark mode from athlete auth pages"

# Push to remote repository
git push origin main
```

### 2. SSH into Production Server

```bash
ssh user@your-production-server.com
cd /path/to/athletegum
```

### 3. Pull Latest Changes

```bash
# Switch to main branch (if not already)
git checkout main

# Pull latest changes
git pull origin main
```

### 4. Install/Update Dependencies

```bash
# Update PHP dependencies
composer install --no-dev --optimize-autoloader

# Update Node dependencies
npm install

# Build frontend assets (IMPORTANT: This includes the Tailwind config changes)
npm run build
```

### 5. Clear All Laravel Caches

```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Optimize (after clearing caches)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Run Database Migrations (if any new migrations)

```bash
# Check for pending migrations
php artisan migrate:status

# Run migrations (if needed)
php artisan migrate --force
```

### 7. Restart PHP-FPM / Web Server

```bash
# For PHP-FPM (adjust service name if different)
sudo systemctl restart php8.3-fpm
# OR
sudo service php8.3-fpm restart

# For Nginx (if needed)
sudo systemctl reload nginx
# OR
sudo service nginx reload
```

### 8. Verify Deployment

- [ ] Visit athlete registration page - text should be visible (light mode)
- [ ] Visit athlete login page - text should be visible (light mode)
- [ ] Test business password reset - should send email via SendGrid
- [ ] Test athlete password reset - should send email via SendGrid
- [ ] Check that dark mode is disabled on all pages

## Important Notes

1. **Frontend Assets**: Always run `npm run build` after pulling changes, especially since we modified `tailwind.config.js`

2. **Caches**: Always clear and rebuild caches after deployment to ensure config changes are picked up

3. **Environment Variables**: Ensure production `.env` has:
   - `MAIL_MAILER=sendgrid`
   - `SENDGRID_API_KEY=your_production_key`
   - `MAIL_FROM_ADDRESS=notifications@athletegum.com`
   - `MAIL_FROM_NAME="AthleteGum"`

4. **Email Testing**: After deployment, test password reset emails to ensure SendGrid is working

5. **Rollback**: If something goes wrong, you can revert:
   ```bash
   git log  # Find the commit hash before your changes
   git reset --hard <previous-commit-hash>
   git push origin main --force  # Only if necessary
   ```

## Quick Deployment Script (if you want to automate)

You can create a `deploy.sh` script on your production server:

```bash
#!/bin/bash
set -e

echo "Deploying to production..."

# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Clear and optimize caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (if any)
php artisan migrate --force

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm

echo "Deployment complete!"
```

Make it executable: `chmod +x deploy.sh`
Then run: `./deploy.sh`


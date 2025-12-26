# Production Fix for Stripe Account Deletion

## Quick Fix Commands (Run on Production Server)

```bash
# 1. SSH into server
ssh athletegum@134.209.167.94

# 2. Go to project directory
cd /var/www/athletegum

# 3. Pull latest code
git pull

# 4. Clear ALL caches (THIS IS CRITICAL)
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# 5. Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Check if route exists
php artisan route:list --name=athlete.earnings.payment-method.destroy
```

## Why It Works Locally But Not in Production

Common reasons:
1. **Route/Config Cache** - Production has cached routes that don't include the new route
2. **View Cache** - Old view files are cached
3. **Database State** - Production might have withdrawal records preventing deletion
4. **File Permissions** - Logs might not be writable

## After Running Commands

1. Try deleting a Stripe account again
2. If it still doesn't work, check logs:
   ```bash
   tail -n 50 storage/logs/laravel.log
   ```

## Alternative: Manual Database Cleanup

If deletion still fails due to withdrawals, you can manually clean up:

```sql
-- Check withdrawals for a payment method
SELECT * FROM athlete_withdrawals WHERE athlete_payment_method_id = [ID];

-- Delete withdrawals (if safe to do so)
DELETE FROM athlete_withdrawals WHERE athlete_payment_method_id = [ID] AND status IN ('completed', 'failed', 'cancelled');

-- Then delete payment method
DELETE FROM athlete_payment_methods WHERE id = [ID];
```


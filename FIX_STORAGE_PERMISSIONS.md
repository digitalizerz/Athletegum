# Fix Storage Permissions on Production Server

## Error
```
file_put_contents(/var/www/athletegum/storage/framework/views/...): Failed to open stream: Permission denied
```

## Solution

SSH into your production server and run these commands:

```bash
# Navigate to your project directory
cd /var/www/athletegum

# Set ownership to web server user (usually www-data for Apache/Nginx)
sudo chown -R www-data:www-data storage bootstrap/cache

# Set proper permissions
sudo chmod -R 775 storage bootstrap/cache

# If the above doesn't work, try more permissive permissions (less secure but will work)
sudo chmod -R 777 storage bootstrap/cache
```

## Alternative: If using a different web server user

If you're using a different web server user (not www-data), find out which user:

```bash
# For Apache
ps aux | grep apache
# or
ps aux | grep httpd

# For Nginx
ps aux | grep nginx
```

Then replace `www-data` with the correct user in the chown command above.

## Verify Permissions

After running the commands, verify:

```bash
ls -la storage/framework/views
ls -la bootstrap/cache
```

You should see the directories are writable by the web server user.

## Clear Cache After Fixing Permissions

```bash
cd /var/www/athletegum
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Test

After fixing permissions, refresh your website. The error should be gone.


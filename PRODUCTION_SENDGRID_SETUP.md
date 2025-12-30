# Production Server SendGrid Setup Guide

## SSL Certificate Configuration

### Most Linux servers handle SSL certificates automatically
Most production Linux servers (Ubuntu, CentOS, Debian, etc.) come with proper CA certificate bundles installed. The SSL certificate issue you experienced on Windows/Laragon is typically **not a problem on Linux production servers**.

### Verify SSL certificates on production

SSH into your production server and run:

```bash
# Check if CA certificates are installed
php -r "echo ini_get('curl.cainfo');"
php -r "echo ini_get('openssl.cafile');"

# On Ubuntu/Debian, certificates are usually at:
# /etc/ssl/certs/ca-certificates.crt

# On CentOS/RHEL, certificates are usually at:
# /etc/pki/tls/certs/ca-bundle.crt
```

### If SSL certificates are missing (rare)

If your production server doesn't have CA certificates, install them:

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install ca-certificates
```

**CentOS/RHEL:**
```bash
sudo yum install ca-certificates
```

## Production .env Configuration

Ensure your production `.env` file has:

```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your_production_sendgrid_api_key_here

MAIL_FROM_ADDRESS=notifications@athletegum.com
MAIL_FROM_NAME="AthleteGum"
```

## Testing on Production

### 1. Test SendGrid connection

Create a test route (remove after testing):

```php
// routes/web.php (temporary test route)
Route::get('/test-sendgrid', function () {
    try {
        Mail::raw('Test email from production', function ($message) {
            $message->to('your-email@example.com')
                    ->subject('Production SendGrid Test');
        });
        return 'Email sent successfully! Check your inbox.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->middleware('auth'); // Protect this route!
```

### 2. Check Laravel logs

```bash
tail -f storage/logs/laravel.log
```

### 3. Check SendGrid dashboard

- Log into SendGrid dashboard
- Go to Activity Feed
- Look for your test email

## Common Production Issues

### Issue: cURL error 77 (SSL certificate)
**Solution:** Install CA certificates (see above)

### Issue: cURL error 6 (Couldn't resolve host)
**Solution:** Check DNS resolution and firewall settings

### Issue: 401 Unauthorized
**Solution:** Verify `SENDGRID_API_KEY` is correct in `.env`

### Issue: 403 Forbidden
**Solution:** Check SendGrid account status and API key permissions

## After Deployment

1. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

2. **Test password reset:**
   - Try the password reset flow
   - Check `storage/logs/laravel.log` for errors
   - Check SendGrid Activity Feed

3. **Monitor logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep -i "password\|sendgrid\|mail"
   ```

## Security Notes

- Never commit `.env` file to version control
- Use different SendGrid API keys for staging and production
- Rotate API keys regularly
- Monitor SendGrid usage and limits


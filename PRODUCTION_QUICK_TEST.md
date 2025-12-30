# Quick Production Server Test Commands

Run these commands directly on your production server to verify SendGrid setup:

## 1. Check SSL Certificates (System Default)

```bash
# Check if system CA certificates exist
ls -la /etc/ssl/certs/ca-certificates.crt 2>/dev/null || ls -la /etc/pki/tls/certs/ca-bundle.crt 2>/dev/null

# Test cURL with system certificates
curl -I https://api.sendgrid.com/v3/mail/send --max-time 5
```

If you see a response (even 401/403), SSL is working. 401/403 is expected without auth headers.

## 2. Check PHP Extensions

```bash
php -m | grep -i curl
php -m | grep -i openssl
```

Both should be listed. If not, install them:
- Ubuntu/Debian: `sudo apt-get install php-curl php-openssl`
- CentOS/RHEL: `sudo yum install php-curl php-openssl`

## 3. Check SendGrid Configuration

```bash
# Navigate to your Laravel project directory first
cd /path/to/your/laravel/project

# Check if .env has SendGrid config
grep -E "MAIL_MAILER|SENDGRID_API_KEY" .env

# Check Laravel config
php artisan tinker --execute="echo 'Mailer: ' . config('mail.default') . PHP_EOL; echo 'SendGrid API Key: ' . (config('services.sendgrid.api_key') ? 'SET' : 'NOT SET') . PHP_EOL;"
```

## 4. Quick SendGrid Test

```bash
# Test SendGrid connection (replace with your email)
php artisan tinker --execute="
use Illuminate\Support\Facades\Mail;
try {
    Mail::raw('Production test', function(\$m) {
        \$m->to('your-email@example.com')->subject('SendGrid Test');
    });
    echo 'SUCCESS: Email sent!';
} catch (\Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage();
}
"
```

## 5. Test Password Reset Flow

After verifying the above, test the actual password reset:
1. Go to `/athlete/forgot-password`
2. Submit an email
3. Check logs: `tail -f storage/logs/laravel.log`
4. Check SendGrid dashboard Activity Feed

## If curl.cainfo is Empty

**This is NORMAL and OK!** When `curl.cainfo` is empty, PHP/cURL uses the system's default CA certificate bundle, which is usually:
- Ubuntu/Debian: `/etc/ssl/certs/ca-certificates.crt`
- CentOS/RHEL: `/etc/pki/tls/certs/ca-bundle.crt`

You only need to set `curl.cainfo` explicitly if:
- The system certificates are missing
- You're getting SSL errors
- You want to use a custom certificate bundle

## Common Issues

### Issue: "cURL error 77"
**Solution:** Install CA certificates:
```bash
# Ubuntu/Debian
sudo apt-get update && sudo apt-get install ca-certificates

# CentOS/RHEL
sudo yum install ca-certificates
```

### Issue: "401 Unauthorized"
**Solution:** Check your `SENDGRID_API_KEY` in `.env` is correct

### Issue: "Couldn't resolve host"
**Solution:** Check DNS/firewall - server can't reach api.sendgrid.com


#!/bin/bash

# Production SendGrid Test Script
# Run this on your production server to verify SendGrid configuration

echo "=== Production SendGrid Configuration Test ==="
echo ""

# Check PHP version
echo "1. PHP Version:"
php -v | head -n 1
echo ""

# Check cURL extension
echo "2. cURL Extension:"
php -m | grep -i curl || echo "❌ cURL extension not found"
echo ""

# Check OpenSSL extension
echo "3. OpenSSL Extension:"
php -m | grep -i openssl || echo "❌ OpenSSL extension not found"
echo ""

# Check SSL certificate paths
echo "4. SSL Certificate Configuration:"
CAINFO=$(php -r "echo ini_get('curl.cainfo');")
CAFILE=$(php -r "echo ini_get('openssl.cafile');")

if [ -n "$CAINFO" ]; then
    echo "   curl.cainfo: $CAINFO"
    if [ -f "$CAINFO" ]; then
        echo "   ✅ Certificate file exists"
    else
        echo "   ❌ Certificate file NOT found"
    fi
else
    echo "   curl.cainfo: (not set - using system default)"
fi

if [ -n "$CAFILE" ]; then
    echo "   openssl.cafile: $CAFILE"
    if [ -f "$CAFILE" ]; then
        echo "   ✅ Certificate file exists"
    else
        echo "   ❌ Certificate file NOT found"
    fi
else
    echo "   openssl.cafile: (not set - using system default)"
fi
echo ""

# Check SendGrid configuration
echo "5. SendGrid Configuration:"
if [ -f ".env" ]; then
    if grep -q "SENDGRID_API_KEY" .env; then
        API_KEY_SET=$(grep "SENDGRID_API_KEY" .env | cut -d '=' -f2)
        if [ -n "$API_KEY_SET" ] && [ "$API_KEY_SET" != "" ]; then
            echo "   ✅ SENDGRID_API_KEY is set"
        else
            echo "   ❌ SENDGRID_API_KEY is empty"
        fi
    else
        echo "   ❌ SENDGRID_API_KEY not found in .env"
    fi
    
    if grep -q "MAIL_MAILER=sendgrid" .env; then
        echo "   ✅ MAIL_MAILER is set to sendgrid"
    else
        echo "   ⚠️  MAIL_MAILER is not set to sendgrid"
    fi
else
    echo "   ❌ .env file not found"
fi
echo ""

# Test cURL to SendGrid API
echo "6. Testing cURL connection to SendGrid API:"
if curl -s -o /dev/null -w "%{http_code}" https://api.sendgrid.com/v3/mail/send --max-time 5 > /dev/null 2>&1; then
    echo "   ✅ Can reach SendGrid API"
else
    echo "   ❌ Cannot reach SendGrid API (check firewall/DNS)"
fi
echo ""

# Check Laravel config
echo "7. Laravel Mail Configuration:"
php artisan tinker --execute="echo 'Default mailer: ' . config('mail.default') . PHP_EOL; echo 'SendGrid configured: ' . (config('services.sendgrid.api_key') ? 'Yes' : 'No') . PHP_EOL;"
echo ""

echo "=== Test Complete ==="
echo ""
echo "If all checks pass, try sending a test email:"
echo "  php artisan tinker"
echo "  >>> Mail::raw('Test', function(\$m) { \$m->to('your@email.com')->subject('Test'); });"


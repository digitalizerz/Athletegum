<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'AthleteGum' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #000000;
            background-color: #ffffff;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            padding: 32px 24px;
            text-align: left;
            border-bottom: 1px solid #e5e5e5;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #000000;
        }
        .content {
            padding: 32px 24px;
        }
        .content p {
            margin: 0 0 16px 0;
            color: #000000;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            margin: 24px 0;
            background-color: #000000;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            font-size: 16px;
        }
        .button:hover {
            background-color: #333333;
        }
        .footer {
            padding: 32px 24px;
            border-top: 1px solid #e5e5e5;
            font-size: 14px;
            color: #666666;
            text-align: center;
        }
        .footer p {
            margin: 0 0 8px 0;
        }
        .footer a {
            color: #000000;
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 24px 16px;
            }
            .header {
                padding: 24px 16px;
            }
            .footer {
                padding: 24px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>AthleteGum</h1>
        </div>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="font-weight: 600; color: #000000; margin-bottom: 12px;">AthleteGum</p>
            <p style="margin-bottom: 16px;">Pay athletes for real work — safely and transparently.</p>
            <p style="margin-bottom: 16px;">This is an automated message. Please do not reply.</p>
            <p style="margin-bottom: 8px;">Need help? <a href="mailto:business@athletegum.com">business@athletegum.com</a></p>
            <p style="margin-top: 16px; font-size: 12px; color: #999999;">© {{ date('Y') }} AthleteGum</p>
        </div>
    </div>
</body>
</html>


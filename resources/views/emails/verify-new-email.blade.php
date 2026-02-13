<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>验证您的新邮箱地址</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a1a1a;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
            color: #555;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            font-size: 14px;
            color: #888;
        }
        .link {
            word-break: break-all;
            color: #2563eb;
        }
        .info-box {
            background-color: #f3f4f6;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>验证您的新邮箱地址</h1>

        <p>您好，{{ $user->name }}：</p>

        <div class="info-box">
            <p style="margin: 0;">我们收到了您更改邮箱地址的请求。请点击下方的按钮验证您的新邮箱地址。</p>
        </div>

        <center>
            <a href="{{ route('admin.email.verify', ['token' => $token]) }}" class="button">验证邮箱地址</a>
        </center>

        <p>或者，您也可以复制以下链接到浏览器地址栏：</p>

        <p class="link">{{ route('admin.email.verify', ['token' => $token]) }}</p>

        <p>此链接将在 60 分钟后过期。如果您没有请求更改邮箱地址，请忽略此邮件。</p>

        <div class="footer">
            <p>此邮件由 {{ config('app.name') }} 自动发送，请勿回复。</p>
        </div>
    </div>
</body>
</html>

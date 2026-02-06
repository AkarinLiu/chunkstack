<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>请确认您的邮箱地址</title>
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
        <h1>请确认您的邮箱地址</h1>

        <p>您好，{{ $user->name }}：</p>

        <div class="info-box">
            <p style="margin: 0;">为确保您的账号安全，我们需要定期确认邮箱地址是否仍在正常使用。您的邮箱已经超过 180 天未确认。</p>
        </div>

        <p>如果您仍在使用此邮箱地址，请点击下方的按钮确认：</p>

        <center>
            <a href="{{ route('admin.email.confirmation.verify', ['token' => $token]) }}" class="button">确认邮箱正常使用</a>
        </center>

        <p>或者，您也可以复制以下链接到浏览器地址栏：</p>

        <p class="link">{{ route('admin.email.confirmation.verify', ['token' => $token]) }}</p>

        <p>此链接将在 60 分钟后过期。</p>

        <p><strong>如果没有收到此邮件？</strong><br>
        如果您没有收到此邮件，或者该邮箱地址已不再使用，请登录系统后点击"没有收到邮件，更改邮箱地址"来更新您的邮箱地址。</p>

        <div class="footer">
            <p>此邮件由 {{ config('app.name') }} 自动发送，请勿回复。</p>
        </div>
    </div>
</body>
</html>

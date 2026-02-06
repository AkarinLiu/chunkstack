<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('admin.password.reset', ['token' => $this->token, 'email' => $notifiable->email]);

        return (new MailMessage)
            ->subject('重置密码')
            ->greeting('您好！')
            ->line('您收到此邮件是因为我们收到了您账户的密码重置请求。')
            ->action('重置密码', $resetUrl)
            ->line('此密码重置链接将在5分钟后过期。')
            ->line('如果您没有请求重置密码，请忽略此邮件。')
            ->salutation('此致');
    }
}

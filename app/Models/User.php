<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_changed_at',
        'email_confirmation_sent_at',
        'email_confirmation_token',
        'pending_email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_confirmation_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_changed_at' => 'datetime',
            'email_confirmation_sent_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * 检查是否需要确认邮箱（180天）
     */
    public function needsEmailConfirmation(): bool
    {
        if (is_null($this->email_changed_at)) {
            return false;
        }

        return $this->email_changed_at->diffInDays(now()) >= 180;
    }

    /**
     * 更新邮箱确认时间
     */
    public function confirmEmailUsage(): void
    {
        $this->update([
            'email_changed_at' => now(),
            'email_confirmation_sent_at' => null,
        ]);
    }

    /**
     * 生成邮箱确认令牌
     */
    public function generateEmailConfirmationToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update([
            'email_confirmation_token' => $token,
            'email_confirmation_sent_at' => now(),
        ]);

        return $token;
    }

    /**
     * 验证确认令牌是否有效
     */
    public function isEmailConfirmationTokenValid(string $token): bool
    {
        return $this->email_confirmation_token === $token
            && $this->email_confirmation_sent_at !== null
            && now()->diffInMinutes($this->email_confirmation_sent_at) <= 60;
    }

    /**
     * 设置待验证邮箱
     */
    public function setPendingEmail(string $email): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update([
            'pending_email' => $email,
            'email_confirmation_token' => $token,
            'email_confirmation_sent_at' => now(),
        ]);

        return $token;
    }

    /**
     * 验证并更新邮箱
     */
    public function verifyAndUpdateEmail(string $token): bool
    {
        if ($this->email_confirmation_token !== $token) {
            return false;
        }

        if ($this->email_confirmation_sent_at === null || now()->diffInMinutes($this->email_confirmation_sent_at) > 60) {
            return false;
        }

        if (is_null($this->pending_email)) {
            return false;
        }

        $this->update([
            'email' => $this->pending_email,
            'email_changed_at' => now(),
            'email_verified_at' => now(),
            'pending_email' => null,
            'email_confirmation_token' => null,
            'email_confirmation_sent_at' => null,
        ]);

        return true;
    }

    /**
     * 清除待验证邮箱
     */
    public function clearPendingEmail(): void
    {
        $this->update([
            'pending_email' => null,
            'email_confirmation_token' => null,
            'email_confirmation_sent_at' => null,
        ]);
    }
}

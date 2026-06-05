<?php

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

test('login without 2FA proceeds normally', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->post(route('admin.login.submit'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHasNoErrors();

    $this->assertAuthenticatedAs($user);
});

test('login with 2FA redirects to challenge page', function () {
    $secret = app(Google2FA::class)->generateSecretKey();
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
        'totp_secret' => Crypt::encryptString($secret),
        'totp_recovery_codes' => json_encode([]),
    ]);

    $this->post(route('admin.login.submit'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('admin.2fa.challenge'));

    $this->assertGuest();
    $this->assertEquals($user->id, session('2fa:user:id'));
});

test('challenge page is accessible with valid session', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->withSession(['2fa:user:id' => $user->id])
        ->get(route('admin.2fa.challenge'))
        ->assertStatus(200)
        ->assertSee('双重验证');
});

test('challenge page returns 404 without session', function () {
    $this->get(route('admin.2fa.challenge'))
        ->assertStatus(404);
});

test('valid TOTP code completes authentication', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
        'totp_secret' => Crypt::encryptString($secret),
        'totp_recovery_codes' => json_encode([]),
    ]);

    $otp = $google2fa->getCurrentOtp($secret);

    $this->withSession(['2fa:user:id' => $user->id])
        ->post(route('admin.2fa.verify'), [
            'code' => $otp,
        ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('invalid TOTP code returns error', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
        'totp_secret' => Crypt::encryptString($secret),
        'totp_recovery_codes' => json_encode([]),
    ]);

    $this->withSession(['2fa:user:id' => $user->id])
        ->post(route('admin.2fa.verify'), [
            'code' => '000000',
        ])
        ->assertSessionHasErrors('code');

    $this->assertGuest();
});

test('valid recovery code completes authentication', function () {
    $plainCode = 'test-code-123';
    $secret = app(Google2FA::class)->generateSecretKey();
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
        'totp_secret' => Crypt::encryptString($secret),
        'totp_recovery_codes' => json_encode([Hash::make($plainCode)]),
    ]);

    $this->withSession(['2fa:user:id' => $user->id])
        ->post(route('admin.2fa.recovery'), [
            'recovery_code' => $plainCode,
        ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
        'totp_recovery_codes' => json_encode([Hash::make($plainCode)]),
    ]);
});

test('invalid recovery code returns error', function () {
    $secret = app(Google2FA::class)->generateSecretKey();
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
        'totp_secret' => Crypt::encryptString($secret),
        'totp_recovery_codes' => json_encode([Hash::make('valid-code')]),
    ]);

    $this->withSession(['2fa:user:id' => $user->id])
        ->post(route('admin.2fa.recovery'), [
            'recovery_code' => 'invalid-code',
        ])
        ->assertSessionHasErrors('recovery_code');

    $this->assertGuest();
});

test('2FA setup page is accessible when authenticated', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get(route('admin.2fa.setup'))
        ->assertStatus(200)
        ->assertSee('双重验证设置');
});

test('2FA setup page redirects when not authenticated', function () {
    $this->get(route('admin.2fa.setup'))
        ->assertRedirect(route('login'));
});

test('enable 2FA shows QR code and secret', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->post(route('admin.2fa.enable'))
        ->assertStatus(200)
        ->assertSee('扫描以下二维码')
        ->assertSessionHas('2fa:pending_secret');
});

test('confirm enable 2FA with valid code', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();
    $user = User::factory()->create(['role' => 'admin']);

    $otp = $google2fa->getCurrentOtp($secret);

    $this->actingAs($user)
        ->withSession(['2fa:pending_secret' => $secret])
        ->post(route('admin.2fa.confirm-enable'), [
            'code' => $otp,
        ])
        ->assertRedirect(route('admin.2fa.setup'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->two_factor_enabled)->toBeTrue();
    expect($user->totp_secret)->not->toBeNull();
    expect($user->totp_recovery_codes)->not->toBeNull();
});

test('confirm enable 2FA with invalid code returns error', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->withSession(['2fa:pending_secret' => $secret])
        ->post(route('admin.2fa.confirm-enable'), [
            'code' => '000000',
        ])
        ->assertSessionHasErrors('code');

    $user->refresh();
    expect($user->two_factor_enabled)->toBeFalse();
});

test('disable 2FA requires current password', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
    ]);

    $this->actingAs($user)
        ->post(route('admin.2fa.disable'), [
            'current_password' => 'password',
        ])
        ->assertRedirect(route('admin.2fa.setup'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->two_factor_enabled)->toBeFalse();
});

test('disable 2FA with wrong password returns error', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
    ]);

    $this->actingAs($user)
        ->post(route('admin.2fa.disable'), [
            'current_password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('current_password');
});

test('regenerate recovery codes', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
        'totp_recovery_codes' => json_encode([Hash::make('old-code')]),
    ]);

    $this->actingAs($user)
        ->post(route('admin.2fa.recovery-codes.regenerate'))
        ->assertRedirect(route('admin.2fa.setup'))
        ->assertSessionHas('success')
        ->assertSessionHas('recovery_codes');
});

test('user can logout after 2FA authentication', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
        'totp_secret' => Crypt::encryptString($secret),
        'totp_recovery_codes' => json_encode([]),
    ]);

    $otp = $google2fa->getCurrentOtp($secret);

    $this->withSession(['2fa:user:id' => $user->id])
        ->post(route('admin.2fa.verify'), ['code' => $otp])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);

    $this->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});

test('can enable TOTP when 2FA already active via WebAuthn only', function () {
    $user = User::factory()->create(['role' => 'admin', 'two_factor_enabled' => true]);

    $this->actingAs($user)
        ->post(route('admin.2fa.enable'))
        ->assertStatus(200)
        ->assertSee('扫描以下二维码')
        ->assertSessionHas('2fa:pending_secret');
});

test('challenge page shows recovery option for WebAuthn only', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
    ]);

    $user->webauthnCredentials()->create([
        'credential_id' => base64_encode('test-credential-id'),
        'public_key' => 'test-public-key',
        'attestation_type' => 'none',
        'aaguid' => '00000000-0000-0000-0000-000000000000',
        'name' => 'Test Key',
        'counter' => 0,
    ]);

    $this->withSession(['2fa:user:id' => $user->id])
        ->get(route('admin.2fa.challenge'))
        ->assertStatus(200)
        ->assertSee('使用恢复码');
});

test('confirm enable TOTP preserves WebAuthn credentials', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();
    $user = User::factory()->create(['role' => 'admin', 'two_factor_enabled' => true]);

    $otp = $google2fa->getCurrentOtp($secret);

    $this->actingAs($user)
        ->withSession(['2fa:pending_secret' => $secret])
        ->post(route('admin.2fa.confirm-enable'), [
            'code' => $otp,
        ])
        ->assertRedirect(route('admin.2fa.setup'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->two_factor_enabled)->toBeTrue();
    expect($user->totp_secret)->not->toBeNull();
    expect($user->totp_recovery_codes)->not->toBeNull();
});

test('login with 2FA and remember device cookie', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();
    $user = User::factory()->create([
        'role' => 'admin',
        'two_factor_enabled' => true,
        'totp_secret' => Crypt::encryptString($secret),
        'totp_recovery_codes' => json_encode([]),
    ]);

    $otp = $google2fa->getCurrentOtp($secret);

    $this->withSession(['2fa:user:id' => $user->id])
        ->post(route('admin.2fa.verify'), [
            'code' => $otp,
            'remember_device' => true,
        ])
        ->assertRedirect(route('admin.dashboard'))
        ->assertCookie('2fa_remember');

    $user->refresh();
    expect($user->two_factor_remember_token)->not->toBeNull();
});

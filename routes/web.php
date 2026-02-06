<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/click/{link}', [HomeController::class, 'click'])->name('click');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('admin')->group(function () {
            Route::resource('categories', CategoryController::class);
            Route::resource('links', LinkController::class);
            Route::resource('tags', TagController::class);

            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [SiteSettingController::class, 'index'])->name('index');
                Route::put('/', [SiteSettingController::class, 'update'])->name('update');
            });
        });

        Route::get('password/change', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
        Route::post('password/change', [AuthController::class, 'changePassword'])->name('password.change.submit');

        // 邮箱相关路由
        Route::get('email/change', [EmailController::class, 'showChangeForm'])->name('email.change');
        Route::post('email/change', [EmailController::class, 'changeEmail'])->name('email.change.submit');
        Route::get('email/verify/{token}', [EmailController::class, 'verifyNewEmail'])->name('email.verify');
        Route::post('email/resend', [EmailController::class, 'resendVerificationEmail'])->name('email.resend');
        Route::post('email/cancel', [EmailController::class, 'cancelEmailChange'])->name('email.cancel');

        // 180天邮箱确认路由
        Route::get('email/confirmation', [EmailController::class, 'showConfirmationForm'])->name('email.confirmation');
        Route::post('email/confirmation/send', [EmailController::class, 'sendConfirmationEmail'])->name('email.confirmation.send');
        Route::get('email/confirmation/verify/{token}', [EmailController::class, 'confirmEmailUsage'])->name('email.confirmation.verify');
    });

    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
        Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [AuthController::class, 'register'])->name('register.submit');

        Route::get('password/forgot', [AuthController::class, 'showForgotPasswordForm'])->name('password.forgot');
        Route::post('password/forgot', [AuthController::class, 'sendResetLinkEmail'])->name('password.forgot.submit');
        Route::get('password/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.submit');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

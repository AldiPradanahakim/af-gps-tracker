<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Password Policy
        |--------------------------------------------------------------------------
        |
        | Minimal 8 karakter, wajib ada huruf besar + huruf kecil, dan wajib
        | ada angka atau karakter khusus. Berlaku untuk semua tempat yang
        | memakai Password::defaults() (registrasi profil, reset password).
        |
        */

        Password::defaults(fn () => Password::min(8)
            ->mixedCase()
            ->rules(['regex:/[\d\W]/']));

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {

            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset Kata Sandi - ' . config('app.name'))
                ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna') . '!')
                ->line('Kami menerima permintaan untuk mereset kata sandi akun GPS Tracker Anda.')
                ->action('Reset Kata Sandi', $url)
                ->line('Tautan ini akan kedaluwarsa dalam ' . config('auth.passwords.users.expire') . ' menit.')
                ->line('Jika Anda tidak meminta reset kata sandi, abaikan email ini, tidak ada perubahan yang dilakukan pada akun Anda.');
        });
    }
}

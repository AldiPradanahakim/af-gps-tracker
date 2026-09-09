<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
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
        | Minimal 8 karakter, wajib ada huruf besar, huruf kecil, angka,
        | DAN karakter khusus. Berlaku untuk semua tempat yang memakai
        | Password::defaults() (lengkapi profil, ubah profil, reset
        | password, profil admin).
        |
        | Daftar syarat ini ditampilkan ke pengguna lewat komponen
        | <x-password-requirements /> - keduanya harus tetap selaras.
        |
        */

        Password::defaults(fn () => Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols());

        /*
        |--------------------------------------------------------------------------
        | API Rate Limiter
        |--------------------------------------------------------------------------
        |
        | Laravel 11+ tidak lagi otomatis mendaftarkan limiter 'api' --
        | didaftarkan manual di sini supaya throttle:api (dipasang lewat
        | $middleware->throttleApi() di bootstrap/app.php) benar-benar aktif.
        |
        */

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {

            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset Kata Sandi - ' . config('app.name'))
                ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna') . '!')
                ->line('Kami menerima permintaan untuk mereset kata sandi akun ' . config('app.name') . ' Anda.')
                ->action('Reset Kata Sandi', $url)
                ->line('Tautan ini akan kedaluwarsa dalam ' . config('auth.passwords.users.expire') . ' menit.')
                ->line('Jika Anda tidak meminta reset kata sandi, abaikan email ini, tidak ada perubahan yang dilakukan pada akun Anda.')
                ->salutation('Salam, ' . config('app.name'));
        });
    }
}

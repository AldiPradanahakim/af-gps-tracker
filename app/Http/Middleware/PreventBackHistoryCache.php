<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistoryCache
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | Cegah browser menyimpan halaman ber-sesi ini di back/forward cache.
        |--------------------------------------------------------------------------
        | Tanpa ini, menekan tombol back setelah aksi yang mengubah data
        | (mis. hapus kendaraan) bisa menampilkan snapshot halaman lama
        | yang sudah tidak sesuai dengan kondisi database saat ini.
        |--------------------------------------------------------------------------
        */

        if (! $request->ajax() && ! $request->wantsJson()) {

            $response->headers->set(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0'
            );

            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}

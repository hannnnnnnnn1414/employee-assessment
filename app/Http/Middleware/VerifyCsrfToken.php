<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Tambahkan jika perlu
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        // Debug log untuk CSRF token
        Log::info('CSRF Token Check', [
            'token_from_session' => $request->session()->token(),
            'token_from_input' => $request->input('_token'),
            'header_token' => $request->header('X-CSRF-TOKEN'),
            'session_id' => $request->session()->getId()
        ]);

        return parent::handle($request, $next);
    }
}

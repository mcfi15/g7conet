<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminSecretMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $secretKey = env('ADMIN_SECRET_KEY');

        if (empty($secretKey)) {
            return $next($request);
        }

        if ($request->session()->get('admin_secret_verified')) {
            return $next($request);
        }

        $submitted = $request->query('admin_secret');
        if ($submitted === $secretKey) {
            $request->session()->put('admin_secret_verified', true);
            return redirect()->to($request->url());
        }

        return response(view('admin.secret', [
            'error' => $submitted !== null ? 'Invalid secret key.' : null,
        ]));
    }
}

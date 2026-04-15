<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // you shall login first!
        if (!auth()->check())
        {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles))
        {
            abort(403, 'Anda tidak memiliki akses ke halaman ini!');
        }

        return $next($request);
    }
}

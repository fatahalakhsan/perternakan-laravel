<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /** Pemakaian di rute: ->middleware('role:pemilik,pekerja') */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Peran Anda tidak boleh mengakses ini.');
        }

        return $next($request);
    }
}

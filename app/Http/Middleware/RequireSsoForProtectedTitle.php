<?php

namespace App\Http\Middleware;

use App\Models\Title;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSsoForProtectedTitle
{
    public function handle(Request $request, Closure $next): Response
    {
        $title = $request->route('title');

        // Público solo si el título existe y requires_sso es explícitamente false.
        if ( $title instanceof Title && $title->requires_sso === false ) {
            return $next($request);
        }

        // Protegido (por defecto): exige un usuario autenticado con Sanctum.
        if ($request->user('sanctum') !== null) {
            return $next($request);
        }

        return response()->json([
            'code' => 'SSO_REQUIRED',
            'message' => 'Authentication is required to access this title.',
        ], 401);
    }
}

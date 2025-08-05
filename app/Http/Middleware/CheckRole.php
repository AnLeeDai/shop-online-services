<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Allow only users whose role_id is in $roles.
     * Usage: ->middleware('role:1')  or  'role:1,2'
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !in_array($user->role_id, $roles)) {
            return response()->json([
                'response_code' => 403,
                'status' => 'error',
                'message' => 'Forbidden',
            ], 403);
        }

        return $next($request);
    }
}

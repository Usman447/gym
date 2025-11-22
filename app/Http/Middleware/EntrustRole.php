<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EntrustRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles  Pipe-separated list of roles (OR logic)
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();

        // Split roles by pipe (OR logic - user needs at least one)
        $roleList = explode('|', $roles);

        // Check if user has any of the required roles
        $hasRole = false;
        foreach ($roleList as $role) {
            $role = trim($role);
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            abort(403, 'You do not have the required role to perform this action.');
        }

        return $next($request);
    }
}


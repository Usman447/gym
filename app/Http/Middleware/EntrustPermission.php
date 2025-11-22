<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EntrustPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permissions  Pipe-separated list of permissions (OR logic)
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();

        // Split permissions by pipe (OR logic - user needs at least one)
        $permissionList = explode('|', $permissions);

        // Check if user has any of the required permissions
        $hasPermission = false;
        foreach ($permissionList as $permission) {
            $permission = trim($permission);
            if ($user->can($permission)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}


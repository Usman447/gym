<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EntrustAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles  Pipe-separated list of roles
     * @param  string  $permissions  Pipe-separated list of permissions
     * @param  string  $validateAll  'true' or 'false' - whether all roles/permissions are required
     * @return mixed
     */
    public function handle($request, Closure $next, $roles, $permissions = null, $validateAll = 'false')
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();
        $validateAll = $validateAll === 'true';

        // Use the ability method from EntrustUserTrait
        $roleList = explode('|', $roles);
        $permissionList = $permissions ? explode('|', $permissions) : [];

        $hasAbility = $user->ability($roleList, $permissionList, [
            'validate_all' => $validateAll,
            'return_type' => 'boolean'
        ]);

        if (!$hasAbility) {
            abort(403, 'You do not have the required ability to perform this action.');
        }

        return $next($request);
    }
}


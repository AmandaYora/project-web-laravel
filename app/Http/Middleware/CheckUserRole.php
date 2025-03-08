<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserRole
{
    private $roleAccess = [
        'admin' => [
            'dashboard' => true,
            'users' => true,
            'projects' => true,
            'tasks' => true,
            'documents' => true,
            'cpm' => true,
            'reports' => true,
            'settings' => true,
        ],
        'manager' => [
            'dashboard' => true,
            'users' => false,
            'projects' => true,
            'tasks' => true,
            'documents' => true,
            'cpm' => true,
            'reports' => true,
            'settings' => true,
        ],
        'pengawas' => [
            'dashboard' => true,
            'users' => false,
            'projects' => false,
            'tasks' => true,
            'documents' => true,
            'cpm' => false,
            'reports' => false,
            'settings' => true,
        ]
    ];

    public function handle(Request $request, Closure $next, $feature)
    {
        $user = session('user');
        $role = strtolower($user->role);

        if (!isset($this->roleAccess[$role]) || !isset($this->roleAccess[$role][$feature])) {
            abort(403, 'Unauthorized action.');
        }

        if (!$this->roleAccess[$role][$feature]) {
            abort(403, 'You do not have permission to access this feature.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->is_admin == 0 && $user->role == 1) {
                auth()->logout();
                return redirect()->route('filament.admin.auth.login')
                    ->withErrors(['email' => 'Bạn không có quyền đăng nhập vào hệ thống này.']);
            }
        }
        
        return $next($request);
    }
}

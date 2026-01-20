<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            // Продлеваем сессию при активности пользователя
            $request->session()->regenerate();
        }
        
        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Редиректим неавторизованных на нашу страницу входа
        if (!$request->expectsJson()) {
            // Проверяем если это админская часть
            if (str_starts_with($request->path(), 'admin')) {
                return route('admin.enter');
            }
            // Для других случаев можно оставить стандартный login
            // или тоже редиректить на admin.enter
            return route('admin.enter');
        }
        
        return null;
    }
}
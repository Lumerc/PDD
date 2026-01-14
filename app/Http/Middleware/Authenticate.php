<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
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
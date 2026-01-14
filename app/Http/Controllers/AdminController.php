<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function enter()
    {
        if (Auth::check()) 
            return redirect()->route('admin.dashboard');
        
        return Inertia::render('Admin/Enter');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember')))
        {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.enter');
    }

    public function dashboard(Request $request) 
    {
        // Явная проверка аутентификации
        if (!Auth::check()) 
        {
            return redirect()->route('admin.enter')->withErrors([
                'auth' => 'Требуется авторизация'
            ]);
        }
        
        // Явная передача данных пользователя
        return Inertia::render('Admin/Dashboard', [
            'auth' => [
                'user' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'created_at' => Auth::user()->created_at->format('d.m.Y'),
                ]
            ]
        ]);
    }
}
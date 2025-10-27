<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('AdminAuth middleware called', [
            'url' => $request->url(),
            'admin_authenticated' => Auth::guard('admin')->check()
        ]);
        
        if (!Auth::guard('admin')->check()) {
            \Log::info('Admin not authenticated, redirecting to login');
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
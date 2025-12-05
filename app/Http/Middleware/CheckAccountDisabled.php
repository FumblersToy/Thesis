<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountDisabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is authenticated and account is disabled
        if ($user && $user->isDisabled()) {
            // Allow access to appeal page and logout
            $allowedRoutes = [
                'account/appeal',
                'logout'
            ];

            $currentPath = $request->path();
            
            foreach ($allowedRoutes as $route) {
                if (str_starts_with($currentPath, $route)) {
                    return $next($request);
                }
            }

            // If trying to access any other route, redirect to settings with error
            return redirect('/settings')->with('error', 'Your account is disabled. You can only submit an appeal.');
        }

        return $next($request);
    }
}

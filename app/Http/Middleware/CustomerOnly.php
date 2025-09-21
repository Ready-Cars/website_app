<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If the authenticated user is an admin, prevent access to customer-only routes
        if ($user && ($user->is_admin ?? false)) {
            // Redirect admins to their dashboard
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}

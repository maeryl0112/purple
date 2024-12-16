<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated
        if ($request->user()) {
            $user = $request->user();

            if (in_array($user->role_id, [1, 2])) {
                return $next($request);
            }

            if ($user->role_id === 3) {
                return redirect()->route('dashboard');
            }

            // Handle other roles or unauthorized access
            return redirect()->route('home')->with('error', 'You are not authorized.');
        }

        // If user is not authenticated, proceed with the request
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BranchAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user(); // Get the logged-in user

        // Allow admins to access all branches
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Ensure the user is an employee
        if ($user->role === 'employee') {
            // Fetch the appointment from the route (if applicable)
            $appointment = $request->route('appointment');

            if ($appointment) {
                // Restrict employees to their assigned branch or assigned appointment branch
                if ($appointment->branch_id != $user->branch_id && $appointment->assigned_branch_id != $user->branch_id) {
                    abort(403, 'Unauthorized Access: You are not allowed to access this appointment.');
                }
            }
        }

        return $next($request);
    }
}

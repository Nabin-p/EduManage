<?php

// In app/Http/Middleware/IsStudent.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated AND their role is 'student'
        if (auth()->check() && auth()->user()->role == 'student') {
            // If yes, allow the request to proceed to the controller
            return $next($request);
        }

        // If not (e.g., an admin trying to access a student URL),
        // abort with a 403 Forbidden error.
        abort(403, 'Unauthorized Access: This page is for students only.');
    }
}
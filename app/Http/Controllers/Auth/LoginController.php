<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    protected function redirectTo()
    {
        // Get the authenticated user's role
        $role = Auth::user()->role;

        // Use a switch statement to check the role
        switch ($role) {
            case 'admin':
                // For an admin, you might have an admin dashboard or just use /home
                return '/home'; // Or '/admin/dashboard'
            case 'teacher':
                // For a teacher, you might have a teacher dashboard or just use /home
                return '/home'; // Or '/teacher/dashboard'
            case 'librarian':
                // This is the one we want to change
                return '/librarian/dashboard';
            case 'student':
                // Students will be redirected to the home page
                return '/home';
            default:
                // As a fallback, redirect to the default home page
                return '/home';
        }
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}

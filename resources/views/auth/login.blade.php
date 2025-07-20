@extends('layouts.app')

@section('content')
<div class="login-bg-gradient min-vh-100 d-flex align-items-center justify-content-center">
    <div class="login-card card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 800px; width: 100%;">
        <div class="row g-0">
            <!-- Left: Welcome -->
            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center p-4 login-welcome-col">
                <div class="text-center">
                    <i class="bi bi-person-circle display-1 mb-3 text-gradient"></i>
                    <h2 class="fw-bold mb-2">Welcome Back!</h2>
                    <p class="mb-0">Sign in to access your dashboard and manage your school efficiently.</p>
                </div>
            </div>
            <!-- Right: Login Form -->
            <div class="col-md-6 bg-dark d-flex align-items-center p-4">
                <div class="w-100">
                    <h3 class="mb-4 text-center text-white">Login</h3>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('E-Mail Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email address">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Login') }}
                            </button>
                        </div>
                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

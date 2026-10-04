<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $cle = Str::lower($request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($cle, 5)) {
            return back()->onlyInput('email')->withErrors([
                'email' => trans('auth.throttle', ['seconds' => RateLimiter::availableIn($cle)]),
            ]);
        }

        if (! Auth::attempt($request->validated(), $request->boolean('remember'))) {
            RateLimiter::hit($cle, 60);

            return back()->onlyInput('email')->withErrors(['email' => trans('auth.failed')]);
        }

        RateLimiter::clear($cle);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

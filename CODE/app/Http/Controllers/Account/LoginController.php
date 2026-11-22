<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\LoginRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Logging in and out (C-07). Follows sequence diagram SQ-01 and activity diagram AD-01.
 *
 * The view resources/views/account/login.blade.php is written by Mẫn.
 * It must post to route('login') with: email, password, remember (checkbox), include @csrf,
 * show session('status') (the message after registering) and the error of "email".
 * A logout button is a small form: POST to route('logout') with @csrf.
 */
class LoginController extends Controller
{
    /** GET /login */
    public function create(): View
    {
        return view('account.login');
    }

    /** POST /login */
    public function store(LoginRequest $request, CartService $carts): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');

        // R-31: the guest cart is found by the session id. Auth::attempt() gives the session
        // a new id as soon as the login succeeds, so the guest's id must be read first.
        $guestSessionId = $request->session()->getId();

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->hitRateLimiter();

            // Same message for a wrong email and a wrong password,
            // so nobody can find out which emails have an account.
            throw ValidationException::withMessages([
                'email' => 'Incorrect email or password.',
            ]);
        }

        // R-23: a locked account cannot log in. The password was correct,
        // so log straight out again and say why.
        if (Auth::user()->isLocked()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Your account is locked. Please contact the store.',
            ]);
        }

        $request->clearRateLimiter();

        // R-31: move the guest cart into the member's cart.
        $merged = $carts->mergeGuestCart($guestSessionId, Auth::user());

        // New session id after login, so an old stolen session id becomes useless.
        $request->session()->regenerate();

        if (Auth::user()->isAdmin()) {
            return redirect()->to('/admin');
        }

        // Back to the page the member wanted before being asked to log in, or the home page.
        $redirect = redirect()->intended(route('home'));

        return $merged
            ? $redirect->with('status', 'The products you added before logging in are now in your cart.')
            : $redirect;
    }

    /** POST /logout */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        // Throw away the session and the form token so they cannot be reused.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

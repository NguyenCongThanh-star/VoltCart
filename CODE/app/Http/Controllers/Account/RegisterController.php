<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Creating a member account (C-06).
 *
 * The view resources/views/account/register.blade.php is written by Mẫn.
 * It must post to route('register') with: name, email, phone, password,
 * password_confirmation, include @csrf, and show the errors of each field ($errors).
 */
class RegisterController extends Controller
{
    /** GET /register */
    public function create(): View
    {
        return view('account.register');
    }

    /** POST /register */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // Only the checked fields are used. "role" and "status" are not fillable,
        // so a new account is always an active customer (see the User model).
        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'password' => $request->validated('password'), // hashed by the User model (R-22)
        ]);

        // The member logs in by themselves on the next page; the email is filled in for them.
        return redirect()
            ->route('login')
            ->with('status', 'Registration successful. Please log in.')
            ->withInput(['email' => $request->validated('email')]);
    }
}

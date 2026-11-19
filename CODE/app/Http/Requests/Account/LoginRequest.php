<?php

namespace App\Http\Requests\Account;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Checks the login form (C-07) and limits wrong attempts.
 *
 * Form fields: email, password, remember (checkbox, optional).
 */
class LoginRequest extends FormRequest
{
    // After this many wrong attempts the visitor must wait (counted per email + IP).
    public const MAX_ATTEMPTS = 5;
    public const DECAY_SECONDS = 60;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            // No strength rules here: old accounts must still be able to log in.
            // The length limit only stops absurdly long input from being hashed.
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
        ]);
    }

    /**
     * Stops with an error if there were too many wrong attempts.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    /** Counts one wrong attempt. */
    public function hitRateLimiter(): void
    {
        RateLimiter::hit($this->throttleKey(), self::DECAY_SECONDS);
    }

    /** Forgets the wrong attempts after a successful login. */
    public function clearRateLimiter(): void
    {
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * The counter is kept per email AND per IP address, so one person typing
     * wrong passwords does not block the same email on other computers.
     */
    private function throttleKey(): string
    {
        return Str::transliterate($this->input('email').'|'.$this->ip());
    }
}

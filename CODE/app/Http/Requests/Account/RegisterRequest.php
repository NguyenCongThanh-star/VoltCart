<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Checks the registration form (C-06) before RegisterController sees it.
 * If a rule fails, Laravel sends the visitor back to the form with the errors
 * and the old input, so the controller only ever receives valid data.
 *
 * Form fields: name, email, phone (optional), password, password_confirmation.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // anyone who is not logged in may register (the "guest" route group checks that)
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // R-22: an email can belong to one account only.
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            // Vietnamese mobile number: 10 digits starting with 0. Optional.
            'phone' => ['nullable', 'regex:/^0[0-9]{9}$/'],
            // "confirmed" means password_confirmation must be the same.
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'phone.regex' => 'The phone number must have 10 digits and start with 0.',
        ];
    }

    /**
     * Tidies the input before the rules run: spaces around the name and email are
     * removed and the email is lower-cased, so " An@Mail.com " and "an@mail.com"
     * count as the same email. Spaces inside the phone number are removed.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'phone' => $this->filled('phone') ? preg_replace('/\s+/', '', (string) $this->input('phone')) : null,
        ]);
    }
}

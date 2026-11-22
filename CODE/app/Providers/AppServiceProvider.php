<?php

namespace App\Providers;

use App\Rules\NotCommonPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->definePasswordRules();
    }

    /**
     * The password rules of the whole site, defined once.
     * Every form that sets a password uses them with Password::defaults():
     * register now, and later "change password" (C-08) and admin forms.
     *
     *   - 8 to 64 characters. Not more than 64 because passwords are stored with bcrypt,
     *     which only reads the first 72 bytes (a Vietnamese letter can take 3 bytes).
     *   - At least one upper-case letter, one lower-case letter, one number and one symbol.
     *   - Not a common password and not a weak word in disguise (App\Rules\NotCommonPassword).
     */
    private function definePasswordRules(): void
    {
        Password::defaults(function () {
            return Password::min(8)
                ->max(64)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->rules([
                    new NotCommonPassword(),
                    // bcrypt ignores everything after 72 bytes, so refuse longer passwords.
                    function (string $attribute, mixed $value, \Closure $fail) {
                        if (strlen((string) $value) > 72) {
                            $fail('The password is too long. Use fewer accented letters or a shorter password.');
                        }
                    },
                ]);
        });
    }
}

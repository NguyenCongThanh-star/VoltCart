<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Refuses passwords that attackers try first, even when they look "strong"
 * (upper case, number, symbol), such as Password@123, P@ssw0rd or Admin@2026.
 *
 * Two checks, both offline (no internet needed):
 *   1. The password itself is in resources/security/common-passwords.txt.
 *   2. The password is a weak word dressed up with numbers and symbols:
 *      "P@ssw0rd!2026" -> "password" -> refused.
 */
class NotCommonPassword implements ValidationRule
{
    // Words that stay weak however many digits or symbols are added around them.
    private const WEAK_WORDS = [
        'password', 'passw', 'pass', 'admin', 'administrator', 'qwerty', 'qwertyuiop',
        'asdf', 'asdfgh', 'zxcvbn', 'abc', 'abcd', 'abcdef', 'abcdefgh', 'aaaa',
        'iloveyou', 'welcome', 'letmein', 'login', 'user', 'test', 'guest', 'root',
        'secret', 'master', 'monkey', 'dragon', 'football', 'sunshine', 'princess',
        'matkhau', 'anhyeuem', 'emyeuanh', 'iloveu', 'voltcart', 'volt', 'cart',
    ];

    // Look-alike characters people use to "hide" a word: P@ssw0rd -> password.
    private const LOOK_ALIKES = [
        '@' => 'a', '4' => 'a', '8' => 'b', '(' => 'c', '3' => 'e', '6' => 'g', '9' => 'g',
        '1' => 'i', '!' => 'i', '|' => 'l', '0' => 'o', '$' => 's', '5' => 's', '7' => 't', '+' => 't', '2' => 'z',
    ];

    private const LIST_FILE = 'security/common-passwords.txt';

    /** @var array<string, true>|null loaded once per request */
    private static ?array $list = null;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = mb_strtolower((string) $value);

        if (isset(self::list()[$password]) || $this->isWeakWordInDisguise($password)) {
            $fail('This password is too common and easy to guess. Please choose another one.');
        }
    }

    private function isWeakWordInDisguise(string $password): bool
    {
        // Cut the digits/symbols added at the start or end ("P@ssw0rd!2026" -> "p@ssw0rd"),
        // then turn look-alikes inside the word back into letters ("p@ssw0rd" -> "password").
        $core = preg_replace('/^[^a-z]+|[^a-z]+$/', '', $password);
        $lettersOnly = preg_replace('/[^a-z]/', '', strtr($core, self::LOOK_ALIKES));
        // Also the letters exactly as typed (digits/symbols dropped), e.g. "Admin@2026" -> "admin".
        $typedLetters = preg_replace('/[^a-z]/', '', $password);

        return in_array($lettersOnly, self::WEAK_WORDS, true)
            || in_array($typedLetters, self::WEAK_WORDS, true);
    }

    /**
     * @return array<string, true>
     */
    private static function list(): array
    {
        if (self::$list === null) {
            $lines = file(resource_path(self::LIST_FILE), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $words = array_map(fn (string $line) => mb_strtolower(trim($line)), $lines);
            $words = array_filter($words, fn (string $w) => $w !== '' && ! str_starts_with($w, '#'));
            self::$list = array_fill_keys($words, true);
        }

        return self::$list;
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen((string) $value) < 8) {
            $fail('Le mot de passe doit contenir au moins 8 caractères.');
            return;
        }
        if (! preg_match('/[A-Z]/', $value)) {
            $fail('Le mot de passe doit contenir au moins une majuscule.');
            return;
        }
        if (! preg_match('/[0-9]/', $value)) {
            $fail('Le mot de passe doit contenir au moins un chiffre.');
            return;
        }
        if (! preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('Le mot de passe doit contenir au moins un caractère spécial.');
        }
    }
}

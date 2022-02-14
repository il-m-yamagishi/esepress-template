<?php

declare(strict_types=1);

namespace App\User;

final class RegisterRequest
{
    public function getValidateRules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'max:64'],
            'password_confirmation' => ['required', 'min:8', 'max:64'],
        ];
    }
}

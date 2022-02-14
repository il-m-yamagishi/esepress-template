<?php

declare(strict_types=1);

namespace App\User;

/**
 * Registering new user transaction
 */
class RegisterTx
{
    public function __invoke(
        string $email,
        string $password,
        string $password_confirmation,
    ): void {
        // do anything...
    }
}

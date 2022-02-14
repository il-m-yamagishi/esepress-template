<?php

declare(strict_types=1);

namespace App\User;

use Semplice\Routing\Get;

class RegisterController
{
    /**
     * Register new user.
     *
     * @param RegisterRequest $request
     * @param RegisterTx $tx
     * @return RegisterResponse
     */
    #[Get('/')]
    public function register(RegisterRequest $request, RegisterTx $tx): RegisterResponse
    {
        $params = $request->all();
        $result = $tx(
            $params['email'],
            $params['password'],
            $params['password_confirmation'],
        );
        return new RegisterResponse($result);
    }
}

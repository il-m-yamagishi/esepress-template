<?php

declare(strict_types=1);

namespace App\User;

class RegisterController
{
    /**
     * Register new user.
     *
     * @param RegisterRequest $request
     * @param RegisterTx $tx
     * @return RegisterResponse
     */
    public function __invoke(RegisterRequest $request, RegisterTx $tx): RegisterResponse
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

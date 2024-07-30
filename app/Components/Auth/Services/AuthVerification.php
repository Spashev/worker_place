<?php

namespace App\Components\Auth\Services;

use App\Components\Auth\Contracts\LoginRequestInterface;
use App\Components\Auth\Contracts\LoginWorkerRequestInterface;
use App\Components\Auth\Contracts\UserVerificationInterface;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;

class AuthVerification implements UserVerificationInterface
{
    public function __construct(
        private readonly Hash $hash,
        private readonly Application $app,
    ) {
    }

    public function isBlocked(BlcaUser $user): bool
    {
        if ($user->block) {
            return true;
        }
        return false;
    }

    public function checkPassword(BlcaUser $user, LoginRequestInterface $request): bool
    {
        //old md5
        if ( $user->password === md5($request->getUserPassword())) {
            return true;
        }

        // new Hash
        if ($this->hash::check($request->getUserPassword(), $user->password)) {
            return true;
        }

        return false;
    }

    public function getWorkerHash(LoginWorkerRequestInterface $request): string
    {
        $hash = md5($request->getUserPassword());

        return $hash;
    }
}

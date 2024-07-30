<?php

namespace App\Components\Auth\Services;

use App\Components\Auth\Contracts\PasswordCreatorInterface;
use App\Components\Auth\Repository\MyMutator;
use App\Components\Auth\Repository\UserQuery;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Support\Str;

class PasswordCreator implements PasswordCreatorInterface
{
    public function __construct(
        private readonly UserQuery $userQuery,
    )
    {}

    public function createPassword(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz123456789';
        $password = '';
        $length = 8;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $password;
    }

    public function createAccessCode(int $numbers): int
    {
        $min = pow(10, $numbers - 1);
        $max = pow(10, $numbers) - 1;
        $code = rand($min, $max);
        $hashedCode = md5($code);

        if ($this->userQuery->userByHash($hashedCode) instanceof BlcaUser) {
            return $this->createAccessCode($numbers);
        }

        return $code;
    }
}
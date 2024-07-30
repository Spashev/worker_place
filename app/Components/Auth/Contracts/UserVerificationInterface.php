<?php

namespace App\Components\Auth\Contracts;

use Bloomex\Common\Blca\Models\BlcaUser;

interface UserVerificationInterface
{
    public function isBlocked(BlcaUser $user): bool;
    public function checkPassword(BlcaUser $user, LoginRequestInterface $request): bool;
}

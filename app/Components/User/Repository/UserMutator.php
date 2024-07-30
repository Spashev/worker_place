<?php

namespace App\Components\User\Repository;

use App\Components\Auth\Services\PasswordCreator;
use App\Components\User\DTO\UserDTO;
use App\Helpers\TimeHelper;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Support\Facades\Hash;

class UserMutator
{
    public function __construct(
        private readonly User            $user,
        private readonly TimeHelper      $timeHelper,
        private readonly Hash            $hash,
        private readonly PasswordCreator $passwordCreator,
    ) {
    }

    public function updateLastVisit(BlcaUser $user): BlcaUser
    {
        $user->lastvisitDate = $this->timeHelper->getDateTimeOfToronto();

        return $user;
    }

    public function create(UserDTO $userDto): User
    {
        $password = $this->passwordCreator->createPassword();

        /** @var User $newUser */
        $this->user->name = $userDto->getName();
        $this->user->username = $userDto->getEmail();
        $this->user->email = $userDto->getEmail();
        $this->user->password = $this->hash::make($password);
        $this->user->usertype = 'Registered';
        $this->user->block = 0;
        $this->user->sendEmail = 0;
        $this->user->gid = 18;
        $this->user->registerDate = $this->timeHelper->getDateTimeOfToronto();
        $this->user->lastvisitDate = $this->timeHelper->getDateTimeOfToronto();
        $this->user->activation = '';
        $this->user->params = '';
        $this->user->rate = 5;

        $this->user->save();

        $this->user->forceFill([
            'password' => $password,
        ]);

        return $this->user;
    }
}

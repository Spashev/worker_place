<?php

namespace App\Components\Auth\Services;

use App\Components\Auth\Contracts\LoginRequestInterface;
use App\Components\Auth\Contracts\LoginWorkerRequestInterface;
use App\Components\Auth\Repository\AuthMutator;
use App\Components\Auth\Repository\UserQuery;
use App\Components\User\Service\UserMutatorService;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;

class AuthService
{
    public function __construct(
        private readonly AuthVerification   $userVerification,
        private readonly UserMutatorService $userService,
        private readonly UserQuery          $query,
        private readonly AuthMutator        $authMutator,
        private readonly Log                $log,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequestInterface $request)
    {
        $user = User::whereEmail($request->getEmail())->first();

        if (!$this->isValid($user, $request)) {
            throw ValidationException::withMessages([
                'message' => [trans('auth.failed')],
            ]);
        }

        $user->tokens()->delete();

        $user = $this->userService->setLastVisit($user);

        $token = $user->createToken('oms-app')->plainTextToken;
        $user->forceFill([
            'token' => $token,
        ]);

        return $user;
    }

    public function loginWorker(LoginWorkerRequestInterface $request): ?BlcaUser
    {
//        $ip =  $request->getClientIp();
//        $ip1 =  $request->ip();

        $hash = $this->userVerification->getWorkerHash($request);
        $user = $this->query->userByHash($hash);

        if (!is_null($user) && !$this->userVerification->isBlocked($user)) {
            $user->tokens()->delete();
            $user = $this->userService->setLastVisit($user);
            $token = $user->createToken('oms-app-packager')->plainTextToken;
            $user->forceFill([
                'token' => $token,
            ]);
        } else {
            throw ValidationException::withMessages([
                'message' => [trans('auth.failed')],
            ]);
        }
        return $user;
    }

    /**
     * @throws Exception
     */
    public function logoutAllUsers(): void
    {
        try {
            $this->authMutator->deleteUsersOMS();
        } catch (Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
    }

    private function isValid(?BlcaUser $user, LoginRequestInterface $request): bool
    {
        return !is_null($user) &&
            !$this->userVerification->isBlocked($user) &&
            $this->userVerification->checkPassword($user, $request);
    }
}

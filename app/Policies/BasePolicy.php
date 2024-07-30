<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BasePolicy
{
    use HandlesAuthorization;

    protected $rolePrefix = '';

    public function __construct()
    {
        $modelName = str_replace('Policy', '', class_basename($this));
        $this->rolePrefix = Str::snake(Str::pluralStudly($modelName));
    }

    /**
     * @param string $functionName
     * @param array $args
     *
     * @return bool
     */
    protected function basePermission(string $functionName, array $args): bool
    {
        $key = null;
        $structure = [
            $this->rolePrefix,
            $functionName
        ];

        if (count($args) > 1 && $args[1] instanceof Model) {
            $model = $args[1];
            $keyName = $model->getKeyName();
            $key = $model->$keyName;
            $structure[] = $key;
        }
        /** @var User $userModel */
        $userModel = $args[0];

        $isAllMethodsPermission = $this->rolePrefix . '.*' . ($key ? '.' . $key : '');
        $isCurrentMethodPermission = implode('.', $structure);

        $result = $userModel->hasAnyPermission($isAllMethodsPermission, $isCurrentMethodPermission);

        return $result;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, mixed $args = [])
    {
        return $this->basePermission(__FUNCTION__, func_get_args());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, mixed $args = [])
    {
        return $this->basePermission(__FUNCTION__, func_get_args());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, mixed $args = [])
    {
        return $this->basePermission(__FUNCTION__, func_get_args());
    }
}
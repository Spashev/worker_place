<?php

namespace App\Http\Resources\Auth;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var User $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'token' => $this->token,
            'email' => $this->email,
            'roles' => $this->getGuardRoleNames(),
            'theme' => isset($this->settings) ? $this->settings->theme : null,
        ];
    }
}

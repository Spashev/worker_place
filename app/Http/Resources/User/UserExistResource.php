<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserExistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        /** @var User $this */
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'name' => $this->name,
            'warehouses' => $this->warehouses->map->only(['warehouse_id', 'warehouse_name'])->values(),
            'roles' => $this->getGuardRoleNames(),
        ];
    }
}
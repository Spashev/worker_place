<?php

namespace App\Http\Resources\User;

use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        /** @var BlcaUser $this */
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
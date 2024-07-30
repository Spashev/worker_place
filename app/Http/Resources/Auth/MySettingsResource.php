<?php

namespace App\Http\Resources\Auth;

use BlcaUserSettings;
use Illuminate\Http\Resources\Json\JsonResource;

class MySettingsResource extends JsonResource
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
        /** @var BlcaUserSettings $this */
        return [
            'user_id' => $this->id,
            'theme' => $this->theme,
            'lang' => $this->lang,
        ];
    }
}
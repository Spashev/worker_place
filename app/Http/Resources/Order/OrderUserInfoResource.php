<?php

namespace App\Http\Resources\Order;

use Bloomex\Common\Blca\Models\BlcaOrderUserInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderUserInfoResource extends JsonResource
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
        /** @var BlcaOrderUserInfo $this */
        return [
            'id' => $this->order_info_id,
            'order_id' => $this->order_id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'country' => $this->countryObj->country_name,
            'city' => $this->city,
            'state' => $this->stateObj->state_name,
            'street_name' =>  $this->street_name,
            'street_number' =>  $this->street_number,
            'suite' => $this->suite,
            'zip' => $this->zip,
            'company' => $this->company,
            'phone' => $this->phone_1,
            'email' => $this->user_email,
            'address_form' => $this->address_type,
            'address_type' => $this->address_type2,
        ];
    }
}
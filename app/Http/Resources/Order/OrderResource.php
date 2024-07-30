<?php

namespace App\Http\Resources\Order;

use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Core\Enums\DeliveryMode;
use Bloomex\Common\Core\Enums\TimeZones;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
        /** @var BlcaOrder $this */
        return [
            'id' => $this->order_id,
            'user_id' => $this->user_id,
            'coupon_code' => $this->coupon_code,
            'coupon_type' => $this->coupon_type,
            'coupon_value' => $this->coupon_value,
            'currency' => $this->order_currency,
            'status' => trans('statuses.' . $this->order_status),
            'occasion'=> isset($this->occasion) ? $this->occasion->order_occasion_name : null,
            'note' => $this->customer_note,
            'comments' => $this->customer_comments,
            'signature' => $this->customer_signature,
            'total' => $this->order_total,
            'subtotal' => $this->order_subtotal,
            'tax' => $this->order_tax,
            'tax_rate' => $this->tax_rate,
            'shipping' => $this->order_shipping,
            'shipping_tax' => $this->order_shipping_tax,
            'discount' => $this->coupon_discount,
            'ip_address' => $this->ip_address,
            'lang' => $this->lang,
            'updater' => $this->username,
            'delivery_mode' => isset($this->shipping_method_normal_id) ?  DeliveryMode::$get[$this->shipping_method_normal_id] : null,
            'created_at' => $this->cdate->timezone(TimeZones::Toronto->value)->toDateTimeString(),
            'updated_at' => $this->mdate->timezone(TimeZones::Toronto->value)->toDateTimeString(),
            'delivered_at' => $this->ddate->format('Y-m-d'),
        ];
    }

}
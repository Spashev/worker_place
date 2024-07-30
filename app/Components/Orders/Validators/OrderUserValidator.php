<?php

namespace App\Components\Orders\Validators;

use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaOrder;

class OrderUserValidator
{
    public function userHasOrderAccess(User $user, BlcaOrder $order): bool
    {
        if ($user->getGuardRoleNames()->contains('Super admin')) {
            return true;
        }
        $usersWarehouses = $user->warehouses()->get()->pluck('warehouse_code');
        if($usersWarehouses->contains($order->warehouse)) {
            return true;
        }
        return false;
    }
}
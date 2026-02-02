<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }
}

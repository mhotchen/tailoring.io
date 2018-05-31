<?php
namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

final class CustomerPolicy
{
    public function interactWith(User $user, Customer $customer): bool
    {
        return $user->worksFor($customer->company);
    }
}

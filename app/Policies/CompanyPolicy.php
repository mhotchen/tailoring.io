<?php
namespace App\Policies;

use App\Models\Company;
use App\Models\User;

final class CompanyPolicy
{
    public function actOnBehalfOf(User $user, Company $company): bool
    {
        return $user->is_active && $user->worksFor($company);
    }
}

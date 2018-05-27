<?php
namespace App\Policies;

use App\Model\Company;
use App\Model\User;

final class CompanyPolicy
{
    public function actOnBehalfOf(User $user, Company $company): bool
    {
        return $user->companies->containsStrict('id', $company->id);
    }
}

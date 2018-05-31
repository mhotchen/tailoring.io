<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Customer;
use App\Policies\CompanyPolicy;
use App\Policies\CustomerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

final class AuthServiceProvider extends ServiceProvider
{
    /** @var array */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Customer::class => CustomerPolicy::class,
    ];

    /**
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(30));
        Passport::refreshTokensExpireIn(now()->addDays(60));
    }
}

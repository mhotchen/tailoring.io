<?php

use App\Measurement\Settings\DefaultMeasurementSetting;
use App\Measurement\Settings\DefaultMeasurementSettings;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerNote;
use App\Models\MeasurementSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class AccountsSeeder extends Seeder
{
    /** @var DefaultMeasurementSettings */
    private $defaultMeasurementSettings;

    public function __construct(DefaultMeasurementSettings $defaultMeasurementSettings)
    {
        $this->defaultMeasurementSettings = $defaultMeasurementSettings;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Expect this to take a while: 500 users/companies, ~500,000 customers, ~2.5 million notes.
         *
         * This means during development missing indexes will be obvious.
         */
        factory(User::class, 500)->make()->each(function (User $user, $i): void {
            $user->email = "test$i@tailoring.io";
            $user->save();

            /** @var Company $company */
            $company = factory(Company::class)->make();
            $user->companies()->save($company);

            $company->measurementSettings()->saveMany(
                (new Collection($this->defaultMeasurementSettings->getSettings()))
                    ->map(function (DefaultMeasurementSetting $default) use ($user) {
                        $setting = new MeasurementSetting;
                        $setting->fillFromDefault($default, $user);
                        return $setting;
                    })
            );

            factory(Customer::class, random_int(900, 1000))
                ->make()
                ->each(function (Customer $customer) use ($user, $company): void {
                    $customer->createdBy()->associate($user);
                    $customer->updatedBy()->associate($user);
                    $customer->company()->associate($company);
                    $customer->save();
                    factory(CustomerNote::class, random_int(0, 10))->make()->each(
                        function (CustomerNote $note) use ($customer, $user, $company): void {
                            $note->createdBy()->associate($user);
                            $note->updatedBy()->associate($user);
                            $note->company()->associate($company);
                            $customer->notes()->save($note);
                        }
                    );
                });
        });
    }
}

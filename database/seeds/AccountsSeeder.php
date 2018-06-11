<?php

use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerNote;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Expect this to take a while: 500 users, ~500,000 customers, ~2.5 million notes.
         *
         * This means during development missing indexes will be obvious.
         */
        factory(User::class, 500)->make()->each(function (User $user, $i): void {
            $user->email = "test$i@tailoring.io";
            $user->save();
            $company = factory(Company::class)->make();
            $user->companies()->save($company);

            factory(Customer::class, random_int(900, 1000))
                ->make()
                ->each(function (Customer $customer) use ($user, $company): void {
                    $customer->createdBy()->associate($user);
                    $customer->updatedBy()->associate($user);
                    $customer->company()->associate($company);
                    $customer->save();
                    factory(CustomerNote::class, random_int(0, 10))->make()->each(
                        function (CustomerNote $note) use ($customer, $user): void {
                            $note->createdBy()->associate($user);
                            $note->updatedBy()->associate($user);
                            $customer->notes()->save($note);
                        }
                    );
                });
        });
    }
}

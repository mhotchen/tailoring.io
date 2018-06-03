<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function run()
    {
        $this->call([
            OAuthClientsSeeder::class,
            AccountsSeeder::class,
        ]);
    }
}

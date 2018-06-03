<?php

use Illuminate\Database\Seeder;

class OAuthClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->insert([
            'id' => 1,
            'name' => 'Tailoring Online Platform Personal Access Client',
            'secret' => 'crTii1u1OJqpZ8HDIA6Dt0YetbDdMRzn6potPrLT',
            'redirect' => 'http://localhost',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]);
        DB::table('oauth_clients')->insert([
            'id' => 2,
            'name' => 'Tailoring Online Platform Password Access Client',
            'secret' => 'vvDmvhD60qbJz5gGOrNJSuxI6bAsS6uNSj8IzF4s',
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ]);
    }
}

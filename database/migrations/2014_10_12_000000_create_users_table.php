<?php

use App\Database\Schema\CustomBlueprint;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Illuminate\Database\QueryException
     */
    public function up()
    {
        Schema::create('users', function (CustomBlueprint $table) {
            $table->uuid('id');
            $table->string('email');
            $table->string('password');
            $table->uuid('email_verification')->nullable();
            $table->enum('status', [
                User::STATUS_AWAITING_EMAIL_VERIFICATION,
                User::STATUS_AWAITING_PASSWORD_RESET,
                User::STATUS_ACTIVE,
            ]);
            $table->timestamps();

            $table->primary('id');
            $table->unique('email');
        });
        DB::unprepared('
            CREATE INDEX users_email_verification ON users (email_verification)
            WHERE (email_verification IS NOT NULL)
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

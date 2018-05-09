<?php

use App\Model\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->uuid('email_verification')->nullable();
            $table->enum('status', [
                User::STATUS_AWAITING_EMAIL_VERIFICATION,
                User::STATUS_AWAITING_PASSWORD_RESET,
                User::STATUS_ACTIVE,
            ]);
            $table->timestamps();

            $table->primary('id');
        });
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

<?php

use App\Database\Schema\CustomBlueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (CustomBlueprint $table) {
            $table->uuid('id');
            $table->string('name');
            $table->timestamps();

            /*
             * Although the company users relationship will be deleted, because users can be part of N companies
             * they aren't automatically deleted so you'll need to delete them by hand if they no longer belong
             * to any particular company.
             */

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
        Schema::dropIfExists('companies');
    }
}

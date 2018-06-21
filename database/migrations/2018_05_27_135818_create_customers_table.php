<?php

use App\Database\Schema\CustomBlueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Illuminate\Database\QueryException
     */
    public function up()
    {
        Schema::create('customers', function (CustomBlueprint $table) {
            $table->uuid('company_id');
            $table->uuid('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by');
            $table->timestamps();

            /*
             * If deleting a full company the customers for that company will be deleted due to the ON DELETE CASCADE
             * constraint.
             *
             * For individual users the created_by/updated_by will need transferred over to another user manually
             * first.
             */

            $table->primary(['company_id', 'id']);

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table
                ->foreign('created_by')
                ->references('id')
                ->on('users');

            $table
                ->foreign('updated_by')
                ->references('id')
                ->on('users');

            $table->index('company_id');
        });

        // We display the most recently updated customers per company on the home page in descending order of when
        // the customer was last updated.
        DB::unprepared('CREATE INDEX customers_sort_index ON customers (company_id ASC, updated_at DESC)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}

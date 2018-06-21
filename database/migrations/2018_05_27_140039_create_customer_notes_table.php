<?php

use App\Database\Schema\CustomBlueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_notes', function (CustomBlueprint $table) {
            $table->uuid('company_id');
            $table->uuid('id');
            $table->uuid('customer_id');
            $table->string('note', 200);
            $table->uuid('created_by');
            $table->uuid('updated_by');
            $table->timestamps();

            /*
             * If deleting a full company the notes for that company will be deleted due to the ON DELETE CASCADE
             * constraint
             *
             * For individual users the created_by/updated_by will need transferred over to another user manually
             * first.
             */

            $table->primary(['company_id', 'id']);

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->foreign(['company_id', 'customer_id'])
                ->references(['company_id', 'id'])
                ->on('customers')
                ->onDelete('cascade');

            $table
                ->foreign('created_by')
                ->references('id')
                ->on('users');

            $table
                ->foreign('updated_by')
                ->references('id')
                ->on('users');

            $table->index('customer_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_notes');
    }
}

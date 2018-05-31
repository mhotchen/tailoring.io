<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('customer_notes', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('note', 200);
            $table->uuid('customer_id');
            $table->uuid('created_by');
            $table->uuid('updated_by');
            $table->timestamps();

            /*
             * If deleting a full company the customers for that company will be deleted due to the ON DELETE CASCADE
             * constraint, then the customer_notes will also be deleted thanks to the cascade on the customer_id
             * below.
             *
             * For individual users the created_by/updated_by will need transferred over to another user manually
             * first.
             */

            $table->primary('id');
            $table->foreign('customer_id')
                ->references('id')
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

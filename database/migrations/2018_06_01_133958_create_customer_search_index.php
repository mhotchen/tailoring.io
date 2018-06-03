<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerSearchIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Illuminate\Database\QueryException
     */
    public function up()
    {
        DB::unprepared('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::unprepared("
            CREATE INDEX customers_search_index ON customers
            USING GIN((
                COALESCE(name, '') ||
                ' ' ||
                COALESCE(email, '') ||
                ' ' ||
                COALESCE(REGEXP_REPLACE(telephone, '[^\+a-zA-Z0-9]', '', 'g'), '')
            ) gin_trgm_ops)
        ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws \Illuminate\Database\QueryException
     */
    public function down()
    {
        DB::unprepared('DROP INDEX customers_search_index');
        DB::unprepared('DROP EXTENSION pg_trgm');
    }
}

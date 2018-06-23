<?php
namespace App\Database\Migrations;

use DB;

trait HandlesConstraints
{
    /**
     * @param string $table
     * @param string $name
     * @param string $checkCommand
     * @throws \Illuminate\Database\QueryException
     */
    public function createConstraint(string $table, string $name, string $checkCommand): void
    {
        DB::unprepared(sprintf('ALTER TABLE %s ADD CONSTRAINT %s CHECK (%s)', $table, $name, $checkCommand));
    }

    /**
     * @param string $table
     * @param string $name
     * @throws \Illuminate\Database\QueryException
     */
    public function dropConstraint(string $table, string $name): void
    {
        DB::unprepared(sprintf('ALTER TABLE %s DROP CONSTRAINT %s', $table, $name));
    }
}
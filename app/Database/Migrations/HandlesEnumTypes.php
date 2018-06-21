<?php
namespace App\Database\Migrations;

use DB;
use MyCLabs\Enum\Enum;

/**
 * This trait provides some helpers for working with enums from a migration class.
 */
trait HandlesEnumTypes
{
    /**
     * @param string       $name
     * @param array|Enum[] $values
     * @throws \Illuminate\Database\QueryException
     */
    public function createEnumType(string $name, array $values): void
    {
        DB::unprepared(sprintf('CREATE TYPE "%s" AS ENUM (%s)', $name, implode(', ', array_map(
            function (Enum $value): string {
                return "'$value'";
            },
            $values
        ))));
    }

    /**
     * @param string $name
     * @throws \Illuminate\Database\QueryException
     */
    public function dropEnumType(string $name): void
    {
        DB::unprepared(sprintf('DROP TYPE IF EXISTS "%s"', $name));
    }
}
<?php
namespace App\Database\Schema\Grammars;

use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Fluent;

final class CustomGrammar extends PostgresGrammar
{
    public function typeGarmentType(Fluent $column): string
    {
        return 'garment_type';
    }

    public function typeGarmentTypeArray(Fluent $column): string
    {
        return 'garment_type[]';
    }

    public function typeMeasurementType(Fluent $column): string
    {
        return 'measurement_type';
    }
}
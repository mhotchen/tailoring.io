<?php
namespace App\Database\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;

final class CustomBlueprint extends Blueprint
{
    public function garmentType(string $column): Fluent
    {
        return $this->addColumn('garmentType', $column);
    }

    public function garmentTypeArray(string $column): Fluent
    {
        return $this->addColumn('garmentTypeArray', $column);
    }

    public function measurementType(string $column): Fluent
    {
        return $this->addColumn('measurementType', $column);
    }
}
<?php

use App\Database\Schema\CustomBlueprint;
use App\Measurement\Settings\UnitOfMeasurementSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddUnitOfMeasurementColumnToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (CustomBlueprint $table) {
            // Inline enum because this is the only place the enum should ever be used in the database. It's only to
            // 'remember' for the frontend.
            $table
                ->enum('unit_of_measurement', [
                    UnitOfMeasurementSetting::CENTIMETERS(),
                    UnitOfMeasurementSetting::INCHES(),
                ])
                ->default(UnitOfMeasurementSetting::CENTIMETERS());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (CustomBlueprint $table) {
            $table->dropColumn('unit_of_measurement');
        });
    }
}

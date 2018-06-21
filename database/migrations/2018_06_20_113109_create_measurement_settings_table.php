<?php

use App\Database\Migrations\HandlesEnumTypes;
use App\Database\Schema\CustomBlueprint;
use App\Garment\GarmentType;
use App\Measurement\MeasurementType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateMeasurementSettingsTable extends Migration
{
    use HandlesEnumTypes;

    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Illuminate\Database\QueryException
     */
    public function up()
    {
        // Don't include ALL enum values because if future migrations add new values then they'll break when doing a
        // fresh migration (it will insert all values immediately then try to add the new value in the migration with
        // an alteration). Remember a migration is a snapshot of a point in time and should not rely on dynamic
        // information.

        $this->createEnumType('garment_type', [
            GarmentType::JACKET(),
            GarmentType::SHIRT(),
            GarmentType::WAISTCOAT(),
            GarmentType::TROUSERS(),
        ]);

        $this->createEnumType('measurement_type', [
            MeasurementType::BODY(),
            MeasurementType::GARMENT(),
            MeasurementType::SAMPLE_ADJUSTMENT(),
            MeasurementType::ALTERATION(),
        ]);

        Schema::create('measurement_settings', function (CustomBlueprint $table) {
            $table->uuid('company_id');
            $table->uuid('id');
            $table->text('name');
            $table->measurementType('type');
            $table->garmentTypeArray('garment_types');
            $table->integer('min_value');
            $table->integer('max_value');
            $table->uuid('created_by');
            $table->uuid('updated_by');
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

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

            $table
                ->foreign('deleted_by')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws \Illuminate\Database\QueryException
     */
    public function down()
    {
        Schema::dropIfExists('measurement_settings');
        $this->dropEnumType('measurement_type');
        $this->dropEnumType('garment_type');
    }
}

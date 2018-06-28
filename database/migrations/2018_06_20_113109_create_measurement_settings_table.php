<?php

use App\Database\Migrations\HandlesConstraints;
use App\Database\Migrations\HandlesEnumTypes;
use App\Database\Schema\CustomBlueprint;
use App\Garment\GarmentType;
use App\Measurement\MeasurementType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateMeasurementSettingsTable extends Migration
{
    use HandlesEnumTypes, HandlesConstraints;

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Throwable
     * @throws \Illuminate\Database\QueryException
     */
    public function up()
    {
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

        // Ensure min_value is smaller than the max_value.
        $this->createConstraint('measurement_settings', 'min_lt_max', '"min_value" < "max_value"');

        // Only BODY measurement settings can have more than one garment type, the others must have exactly one.
        // The coalesce is because array_length returns NULL for empty arrays which when compared to a number returns
        // NULL which isn't an explicit FALSE value; constraints always pass unless the returned value is a proper
        // FALSE.
        $this->createConstraint(
            'measurement_settings',
            'garment_type_count',
            sprintf(
                '
                ("type" = \'%s\' AND COALESCE(ARRAY_LENGTH("garment_types", 1), 0) >= 1)
                OR
                ("type" != \'%s\' AND COALESCE(ARRAY_LENGTH("garment_types", 1), 0) = 1)
                ',
                MeasurementType::BODY(),
                MeasurementType::BODY()
            )
        );

        // BODY and GARMENT measurements cannot be less than 0, SAMPLE_ADJUSTMENT and ALTERATION are adjustments to
        // an existing measurement so they can be negative values.
        $this->createConstraint(
            'measurement_settings',
            'min_length_min_value',
            sprintf(
                '
                ("type" = ANY(ARRAY[\'%s\', \'%s\']::measurement_type[]) AND min_value >= 0)
                OR
                ("type" = ANY(ARRAY[\'%s\', \'%s\']::measurement_type[]))
                ',
                MeasurementType::BODY(),
                MeasurementType::GARMENT(),
                MeasurementType::SAMPLE_ADJUSTMENT(),
                MeasurementType::ALTERATION()
            )
        );
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

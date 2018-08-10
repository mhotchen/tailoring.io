<?php

use App\Database\Migrations\HandlesConstraints;
use App\Database\Migrations\HandlesEnumTypes;
use App\Database\Schema\CustomBlueprint;
use App\Measurement\Profile\MeasurementProfileType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateMeasurementProfileTables extends Migration
{
    use HandlesEnumTypes, HandlesConstraints;

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Throwable
     */
    public function up()
    {
        $this->createEnumType('measurement_profile_type', [
            MeasurementProfileType::BODY(),
            MeasurementProfileType::GARMENT(),
        ]);

        Schema::create('measurement_profiles', function (CustomBlueprint $table) {
            $table->uuid('company_id');
            $table->uuid('id');
            $table->uuid('customer_id');
            $table->measurementProfileType('type');
            $table->garmentType('garment')->nullable();
            $table->uuid('created_by');
            $table->uuid('deleted_by')->nullable();
            $table->timestamp('created_at');
            $table->softDeletes();

            $table->primary(['company_id', 'id']);

            $table->foreign(['company_id', 'customer_id'])
                ->references(['company_id', 'id'])
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table
                ->foreign('created_by')
                ->references('id')
                ->on('users');

            $table
                ->foreign('deleted_by')
                ->references('id')
                ->on('users');
        });

        // BODY profiles must not be associated with any specific garment.
        // GARMENT profiles must have the garment they're used with set.
        $this->createConstraint(
            'measurement_profiles',
            'garment_profile_has_garment',
            sprintf(
                '("type" = \'%s\' AND garment IS NULL) OR ("type" = \'%s\' AND garment IS NOT NULL)',
                MeasurementProfileType::BODY(),
                MeasurementProfileType::GARMENT()
            )
        );

        // There can only be one BODY profile per customer.
        $this->partialUnique(
            'measurement_profiles',
            ['company_id', 'customer_id', 'type'],
            'one_body_profile_per_customer',
            sprintf('type = \'%s\'', MeasurementProfileType::BODY())
        );

        Schema::create('measurement_profile_commits', function (CustomBlueprint $table) {
            $table->uuid('company_id');
            $table->uuid('id');
            $table->uuid('measurement_profile_id');
            $table->integer('revision');
            $table->uuid('sample_garment_id')->nullable();
            $table->text('name');
            $table->text('message')->nullable();
            $table->uuid('created_by');
            $table->timestamp('created_at');

            $table->primary(['company_id', 'id']);
            $table->unique(['company_id', 'measurement_profile_id', 'revision']);

            $table->foreign(['company_id', 'measurement_profile_id'])
                ->references(['company_id', 'id'])
                ->on('measurement_profiles')
                ->onDelete('cascade');

            $table->foreign(['company_id', 'sample_garment_id'])
                ->references(['company_id', 'id'])
                ->on('sample_garments');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table
                ->foreign('created_by')
                ->references('id')
                ->on('users');
        });

        Schema::create('measurement_profile_measurements', function (CustomBlueprint $table) {
            $table->uuid('company_id');
            $table->uuid('id');
            $table->uuid('measurement_profile_commit_id');
            $table->uuid('measurement_setting_id');
            $table->integer('value')->nullable();
            $table->text('comment')->nullable();
            $table->uuid('created_by');
            $table->timestamp('created_at');

            $table->primary(['company_id', 'id']);

            // The default index name gets truncated and ended up matching the FK name below.
            $table->unique(
                ['company_id', 'measurement_profile_commit_id', 'measurement_setting_id'],
                'unique_company_commit_setting'
            );

            $table->foreign(['company_id', 'measurement_profile_commit_id'])
                ->references(['company_id', 'id'])
                ->on('measurement_profile_commits')
                ->onDelete('cascade');

            $table->foreign(['company_id', 'measurement_setting_id'])
                ->references(['company_id', 'id'])
                ->on('measurement_settings');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table
                ->foreign('created_by')
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
        Schema::dropIfExists('measurement_profile_measurements');
        Schema::dropIfExists('measurement_profile_commits');
        Schema::dropIfExists('measurement_profiles');
        $this->dropEnumType('measurement_profile_type');
    }
}

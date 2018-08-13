<?php
namespace App\Models;

use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * App\Models\MeasurementProfileCommit
 *
 * @property string $company_id
 * @property string $id
 * @property string $measurement_profile_id
 * @property int $revision
 * @property string|null $sample_garment_id
 * @property string $name
 * @property string $message
 * @property string $created_by
 * @property \Carbon\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasurementProfileMeasurement[] $measurements
 * @property-read \App\Models\SampleGarment|null $sampleGarment
 * @property-read \App\Models\User $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereMeasurementProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereRevision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileCommit whereSampleGarmentId($value)
 * @mixin \Eloquent
 */
final class MeasurementProfileCommit extends Model
{
    /**
     * A bit of a hack to disable the updated_at column whilst still allowing Eloquent to manage the created_at
     * column.
     *
     * @see HasTimestamps::updateTimestamps
     */
    public const UPDATED_AT = null;

    use Compoships;

    /** @var array */
    protected $casts = ['id' => 'string'];

    public function sampleGarment(): BelongsTo
    {
        return $this->belongsTo(
            SampleGarment::class,
            ['company_id', 'sample_garment_id'],
            ['company_id', 'id']
        );
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(
            MeasurementProfileMeasurement::class,
            ['company_id', 'measurement_profile_commit_id'],
            ['company_id', 'id']
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Checks if the commit/measurements have actually changed anything within the profile.
     *
     * @param MeasurementProfile $profile
     * @param Collection         $measurements
     * @return bool
     */
    public function hasNoChanges(MeasurementProfile $profile, Collection $measurements): bool
    {
        return
            $profile->current_name === $this->name &&
            $measurements->isEmpty() &&
            $this->sampleGarment->is($profile->current_sample_garment)
        ;
    }

    public function fillFromRequest(
        array $request,
        MeasurementProfile $profile,
        ?SampleGarment $sampleGarment,
        Company $company,
        User $createdBy
    ): void
    {
        $this->id = $request['data']['id'];
        $this->name = $request['data']['name'];
        $this->message = $request['data']['message'] ?? null;
        // Thanks lack of composite key support! I really appreciate it and the terrible running of Laravel from
        // Taylor wrt to closing any tickets suggesting this feature because he doesn't like using the full
        // capability of databases...
        // Despite what the cargo cult says using composite keys isn't an anti-pattern and can be extremely effective,
        // such as in this situation wherein it makes it impossible to mess with other application tenants by
        // screwing with data sent to the API whilst allowing offline UID generation.
        if ($sampleGarment) {
            $this->sample_garment_id = $sampleGarment->id;
        }
        $this->revision = \DB::raw("
            COALESCE(
                (
                    SELECT MAX(revision)
                    FROM measurement_profile_commits
                    WHERE company_id = '$company->id'
                    AND measurement_profile_id = '$profile->id'
                ),
                0
            ) + 1
        ");
        $this->company()->associate($company);
        $this->createdBy()->associate($createdBy);
    }
}

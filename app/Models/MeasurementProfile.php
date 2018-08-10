<?php
namespace App\Models;

use App\Garment\GarmentType;
use App\Measurement\Profile\MeasurementProfileType;
use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * App\Models\MeasurementProfile
 *
 * @property string $company_id
 * @property string $id
 * @property string $customer_id
 * @property MeasurementProfileType $type
 * @property GarmentType|null $garment
 * @property string $created_by
 * @property string $deleted_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasurementProfileCommit[] $commits
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\User $deletedBy
 * @property-read Collection|MeasurementProfileMeasurement[] $current_measurements
 * @property-read string|null $current_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfile whereGarment($value)
 * @mixin \Eloquent
 */
final class MeasurementProfile extends Model
{
    /**
     * A bit of a hack to disable the updated_at column whilst still allowing Eloquent to manage the created_at
     * column.
     *
     * @see HasTimestamps::updateTimestamps
     */
    public const UPDATED_AT = null;

    use GeneratesUniqueUuid, Compoships;

    /** @var array */
    protected $casts = ['id' => 'string'];

    /** @var array */
    protected $dates = ['created_at', 'deleted_at'];

    public function commits(): HasMany
    {
        return $this->hasMany(
            MeasurementProfileCommit::class,
            ['company_id', 'measurement_profile_id'],
            ['company_id', 'id']
        );
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Virtual attribute that returns the latest measurements for this profile, with the array index being the
     * measurement_setting_id for quicker lookups.
     *
     * @return Collection|MeasurementProfileMeasurement[]
     */
    public function getCurrentMeasurementsAttribute(): Collection
    {
        // 'keyBy' replaces items in collection if same key exists, so if a measurement exists in multiple commits
        // for the same setting then the newer one is used (assuming correct ordering, which is true by default).
        return $this->commits
            ->flatMap(function (MeasurementProfileCommit $commit): iterable { return $commit->measurements; })
            ->keyBy('measurement_setting_id')
        ;
    }

    /**
     * Virtual attribute that returns the name as it is set in the latest commit.
     *
     * @return null|string
     */
    public function getCurrentNameAttribute(): ?string
    {
        return $this->commits->last()->name;
    }

    /**
     * @return MeasurementProfileType
     * @throws \UnexpectedValueException
     */
    public function getTypeAttribute(): MeasurementProfileType
    {
        return new MeasurementProfileType($this->attributes['type']);
    }

    /**
     * @return GarmentType|null
     * @throws \UnexpectedValueException
     */
    public function getGarmentAttribute(): ?GarmentType
    {
        return $this->attributes['garment'] === null ? null : new GarmentType($this->attributes['garment']);
    }

    /**
     * @param Company $company
     * @param User    $createdBy
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fillForBodyProfile(Company $company, User $createdBy): void
    {
        $this->id = static::uniqueUuid();
        $this->type = MeasurementProfileType::BODY();
        $this->garment = null;
        $this->createdBy()->associate($createdBy);
        $this->company()->associate($company);
    }

    public function filterMeasurement(MeasurementProfileMeasurement $measurement): bool
    {
        /** @var MeasurementProfileMeasurement|null $current */
        $current = $this->current_measurements->get($measurement->measurement_setting_id);

        // New measurement: if any of the values are set then don't filter.
        if (!$current) {
            return $measurement->value !== null || $measurement->comment !== null;
        }

        // Existing measurement: if any of the values have changed then don't filter.
        return $current->value !== $measurement->value || $current->comment !== $measurement->comment;
    }
}

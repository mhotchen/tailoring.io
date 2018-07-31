<?php
namespace App\Models;

use App\Garment\GarmentType;
use App\Measurement\Profile\MeasurementProfileType;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    use GeneratesUniqueUuid;

    /** @var array */
    protected $casts = ['id' => 'string'];

    public function commits(): HasMany
    {
        return $this->hasMany(MeasurementProfileCommit::class);
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
}

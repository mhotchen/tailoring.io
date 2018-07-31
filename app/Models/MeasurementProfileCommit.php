<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /** @var array */
    protected $casts = ['id' => 'string'];

    public function sampleGarment(): BelongsTo
    {
        return $this->belongsTo(SampleGarment::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(MeasurementProfileMeasurement::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

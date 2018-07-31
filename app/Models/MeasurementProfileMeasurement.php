<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\MeasurementProfileMeasurement
 *
 * @property string $company_id
 * @property string $id
 * @property string $measurement_profile_commit_id
 * @property string $measurement_setting_id
 * @property int|null $value
 * @property string $comment
 * @property string $created_by
 * @property \Carbon\Carbon $created_at
 * @property-read \App\Models\MeasurementSetting $measurementSetting
 * @property-read \App\Models\User $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileMeasurement whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileMeasurement whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileMeasurement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileMeasurement whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileMeasurement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileMeasurement whereMeasurementProfileCommitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileMeasurement whereMeasurementSettingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementProfileMeasurement whereValue($value)
 * @mixin \Eloquent
 */
final class MeasurementProfileMeasurement extends Model
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

    public function measurementSetting(): BelongsTo
    {
        return $this->belongsTo(MeasurementSetting::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

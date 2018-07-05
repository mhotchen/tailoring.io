<?php

namespace App\Models;

use App\Garment\GarmentType;
use App\Measurement\MeasurementType;
use App\Measurement\Settings\DefaultMeasurementSetting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * NB: This model can't be updated using the ide-helper because of the custom types so needs manually maintained.
 *
 * It's worth the effort to maintain this though so please do.
 *
 * App\Models\MeasurementSetting
 *
 * @property string                                                                    $company_id
 * @property string                                                                    $id
 * @property string                                                                    $name
 * @property MeasurementType                                                           $type
 * @property GarmentType[]|Collection                                                  $garment_types
 * @property int                                                                       $min_value
 * @property int                                                                       $max_value
 * @property \Carbon\Carbon|null                                                       $created_at
 * @property \Carbon\Carbon|null                                                       $updated_at
 * @property \Carbon\Carbon|null                                                       $deleted_at
 * @property-read \App\Models\User                                                     $createdBy
 * @property-read \App\Models\User                                                     $updatedBy
 * @property-read \App\Models\User                                                     $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementSetting whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MeasurementSetting whereDeletedAt($value)
 * @mixin \Eloquent
 */
final class MeasurementSetting extends Model
{
    use HandlesPostgresArrays, GeneratesUniqueUuid;

    /** @var array */
    protected $casts = ['id' => 'string'];

    /** @var array */
    protected $fillable = ['name'];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * @return MeasurementType
     * @throws \UnexpectedValueException
     */
    public function getTypeAttribute(): MeasurementType
    {
        return new MeasurementType($this->attributes['type']);
    }

    /**
     * @param iterable|GarmentType[] $garmentTypes
     * @throws \InvalidArgumentException
     */
    public function setGarmentTypesAttribute(iterable $garmentTypes): void
    {
        $this->attributes['garment_types'] = $this->toPostgresArray($garmentTypes);
    }

    /**
     * @return Collection|GarmentType[]
     * @throws \InvalidArgumentException
     */
    public function getGarmentTypesAttribute(): Collection
    {
        return (new Collection($this->fromPostgresArray($this->attributes['garment_types'])))
            ->map(function (string $garmentType): GarmentType {
                return new GarmentType($garmentType);
            });
    }

    /**
     * @param DefaultMeasurementSetting $default
     * @param User                      $user
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fillFromDefault(DefaultMeasurementSetting $default, User $user): void
    {
        $this->id = static::uniqueUuid();
        $this->createdBy()->associate($user);
        $this->updatedBy()->associate($user);
        $this->name = trans($default->getName());
        $this->type = $default->getType();
        $this->garment_types = $default->getGarmentTypes();
        $this->min_value = $default->getMinValue();
        $this->max_value = $default->getMaxValue();
    }

    /**
     * @param User $deletedBy
     * @throws \InvalidArgumentException
     */
    public function softDelete(User $deletedBy): void
    {
        $this->deletedBy()->associate($deletedBy);
        $this->deleted_at = Carbon::now();
        $this->save();
    }
}

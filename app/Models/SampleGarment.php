<?php
namespace App\Models;

use App\Garment\GarmentType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * NB: This model can't be updated using the ide-helper because of the custom types so needs manually maintained.
 *
 * It's worth the effort to maintain this though so please do.
 *
 * App\Models\SampleGarment
 *
 * @property string                                                                    $company_id
 * @property string                                                                    $id
 * @property string                                                                    $name
 * @property GarmentType                                                               $garment
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
final class SampleGarment extends Model
{
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
     * @return GarmentType
     * @throws \UnexpectedValueException
     */
    public function getTypeAttribute(): GarmentType
    {
        return new GarmentType($this->attributes['garment']);
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

    /**
     * @param array $request
     * @param User  $user
     */
    public function hydrateFromRequest(array $request, User $user)
    {
        $this->id = $request['data']['id'];
        $this->createdBy()->associate($user);
        $this->updatedBy()->associate($user);
        $this->name = $request['data']['name'];
        $this->garment = $request['data']['garment'];
    }
}

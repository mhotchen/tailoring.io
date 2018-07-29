<?php
namespace App\Models;

use App\Measurement\Settings\UnitOfMeasurementSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;

/**
 * App\Models\Company
 *
 * @property string                                                                         $id
 * @property string                                                                         $name
 * @property UnitOfMeasurementSetting                                                       $unit_of_measurement
 * @property \Carbon\Carbon|null                                                            $created_at
 * @property \Carbon\Carbon|null                                                            $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[]               $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Customer[]           $customers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasurementSetting[] $measurementSettings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SampleGarment[]      $sampleGarments
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereUnitOfMeasurement($value)
 * @mixin \Eloquent
 */
final class Company extends Model
{
    use GeneratesUniqueUuid;

    /** @var array */
    protected $fillable = ['name', 'unit_of_measurement'];

    /** @var array */
    protected $casts = ['id' => 'string'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function measurementSettings(): HasMany
    {
        return $this->hasMany(MeasurementSetting::class);
    }

    public function sampleGarments(): HasMany
    {
        return $this->hasMany(SampleGarment::class);
    }

    /**
     * @return UnitOfMeasurementSetting
     * @throws \UnexpectedValueException
     */
    public function getUnitOfMeasurementAttribute(): UnitOfMeasurementSetting
    {
        return new UnitOfMeasurementSetting($this->attributes['unit_of_measurement']);
    }

    /**
     * @param string $searchTerm
     * @return Collection|Customer[]
     * @throws \InvalidArgumentException
     */
    public function findCustomers(string $searchTerm = null): Collection
    {
        $query = $this->customers()->limit(20);

        // Normalize whitespace and explode in to different tokens.
        $tokens = explode(' ', preg_replace('/\s+/', ' ', trim($searchTerm ?? '')));

        foreach ($tokens as $token) {
            /*
             * Thanks to Postgres the following matches an index.
             *
             * If you need to modify it then don't forget to update the index!
             */
            $query->whereRaw(
                "
                    COALESCE(name, '') ||
                    ' ' ||
                    COALESCE(email, '') ||
                    ' ' ||
                    COALESCE(REGEXP_REPLACE(telephone, '[^\+a-zA-Z0-9]', '', 'g'), '')
                    ~~* ?
                ",
                ["%$token%"]
            );
        }

        return $query->get();
    }

    public function roundMeasurementSettingsToUnitOfMeasurement(User $updatedBy): void
    {
        $this->measurementSettings()->update([
            'min_value' => $this->getRoundMeasurementToNearestUnitOfMeasurementExpression('min_value'),
            'max_value' => $this->getRoundMeasurementToNearestUnitOfMeasurementExpression('max_value'),
            'updated_by' => $updatedBy->id,
        ]);
    }

    /**
     * @param array $validatedRequestPayload
     * @return self
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public static function fromRequest(array $validatedRequestPayload): self
    {
        $company = new self;
        $company->id = static::uniqueUuid();
        $company->name = $validatedRequestPayload['data']['name'];
        $company->unit_of_measurement = UnitOfMeasurementSetting::DEFAULT();

        return $company;
    }

    private function getRoundMeasurementToNearestUnitOfMeasurementExpression(string $column): Expression
    {
        return new Expression(sprintf(
            'ROUND(%s / %2$d.0) * %2$d',
            $column,
            $this->unit_of_measurement->getRoundMeasurementToNearestValue()
        ));
    }
}

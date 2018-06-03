<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Company
 *
 * @property string                                                               $id
 * @property string                                                               $name
 * @property \Carbon\Carbon|null                                                  $created_at
 * @property \Carbon\Carbon|null                                                  $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[]     $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Customer[] $customers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereUpdatedAt($value)
 * @mixin \Eloquent
 */
final class Company extends Model
{
    use GeneratesUniqueUuid;

    /** @var array */
    protected $fillable = ['name'];

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

        return $company;
    }
}

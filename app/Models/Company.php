<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

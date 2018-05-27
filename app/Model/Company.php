<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Company whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Model\User[] $users
 * @mixin \Eloquent
 */
final class Company extends Model
{
    /** @var array */
    protected $fillable = ['name'];

    /** @var array */
    protected $casts = ['id' => 'string'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @param array $validatedRequestPayload
     * @return self
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public static function fromRequest(array $validatedRequestPayload): self
    {
        $company = new self;
        $company->id = Uuid::uuid4();
        $company->name = $validatedRequestPayload['data']['name'];

        return $company;
    }
}

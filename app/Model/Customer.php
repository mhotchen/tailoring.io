<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $telephone
 * @property string $company_id
 * @property string $created_by
 * @property string $updated_by
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Model\Company $company
 * @property-read \App\Model\User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Model\CustomerNote[] $notes
 * @property-read \App\Model\User $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Customer whereUpdatedBy($value)
 * @mixin \Eloquent
 */
final class Customer extends Model
{
    /** @var array */
    protected $fillable = ['name', 'email', 'telephone'];

    /** @var array */
    protected $casts = ['id' => 'string'];

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @param array   $request
     * @param User    $createdBy
     * @param Company $company
     * @return Customer
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public static function fromRequest(array $request, User $createdBy, Company $company): self
    {
        $customer = new self;
        $customer->id = Uuid::uuid4();
        $customer->name = $request['data']['name'];
        $customer->createdBy()->associate($createdBy);
        $customer->updatedBy()->associate($createdBy);
        $customer->company()->associate($company);

        if (isset($request['data']['email'])) {
            $customer->email = $request['data']['email'];
        }

        if (isset($request['data']['telephone'])) {
            $customer->telephone = $request['data']['telephone'];
        }

        return $customer;
    }

}

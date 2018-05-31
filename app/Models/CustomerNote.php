<?php
namespace App\Models;

use App\Models\Scopes\OrderByScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string                $id
 * @property string                $note
 * @property string                $customer_id
 * @property string                $created_by
 * @property string                $updated_by
 * @property \Carbon\Carbon|null   $created_at
 * @property \Carbon\Carbon|null   $updated_at
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\User $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerNote whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerNote whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerNote whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerNote whereUpdatedBy($value)
 * @mixin \Eloquent
 */
final class CustomerNote extends Model
{
    use GeneratesUniqueUuid;

    /** @var array */
    protected $fillable = ['note'];

    /** @var array */
    protected $casts = ['id' => 'string'];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new OrderByScope('created_at', 'asc'));
    }

    /**
     * @param array $request
     * @param User  $updatedBy
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function hydrateFromRequest(array $request, User $updatedBy): void
    {
        $this->fill($request['data']);
        if (!$this->exists) {
            $this->id = static::uniqueUuid();
            $this->createdBy()->associate($updatedBy);
        }

        if ($this->isDirty()) {
            $this->updatedBy()->associate($updatedBy);
        }
    }
}

<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property string $note
 * @property string $customer_id
 * @property string $created_by
 * @property string $updated_by
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Model\User $createdBy
 * @property-read \App\Model\User $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\CustomerNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\CustomerNote whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\CustomerNote whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\CustomerNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\CustomerNote whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\CustomerNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\CustomerNote whereUpdatedBy($value)
 * @mixin \Eloquent
 */
final class CustomerNote extends Model
{
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
     * @param array $request
     * @param User  $createdBy
     * @return CustomerNote
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public static function fromRequest(array $request, User $createdBy): self
    {
        $note = new self;
        $note->id = Uuid::uuid4();
        $note->note = $request['data']['note'];
        $note->createdBy()->associate($createdBy);
        $note->updatedBy()->associate($createdBy);

        return $note;
    }
}

<?php
namespace App\Models;

use App\Models\Scopes\OrderByScope;
use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * App\Models\Customer
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $telephone
 * @property string $company_id
 * @property string $created_by
 * @property string $updated_by
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\User $updatedBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomerNote[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasurementProfile[] $measurementProfiles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereUpdatedBy($value)
 * @mixin \Eloquent
 */
final class Customer extends Model
{
    use Compoships;

    /** @var array */
    protected $fillable = ['id', 'name', 'email', 'telephone'];

    /** @var array */
    protected $casts = ['id' => 'string'];

    public function notes(): HasMany
    {
        return $this->hasMany(
            CustomerNote::class,
            ['company_id', 'customer_id'],
            ['company_id', 'id']
        );
    }

    public function measurementProfiles(): HasMany
    {
        return $this->hasMany(
            MeasurementProfile::class,
            ['company_id', 'customer_id'],
            ['company_id', 'id']
        );
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
     * @throws \InvalidArgumentException
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new OrderByScope('updated_at', 'DESC'));
    }

    /**
     * @param array        $request
     * @param User         $user
     * @param Company|null $company Only required when creating a new customer, otherwise it's ignored.
     * @return $this
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \LogicException
     */
    public function hydrateFromRequest(array $request, User $user, Company $company = null): self
    {
        // Update the customer
        $this->fill($request['data']);

        if (!$this->exists) {
            $this->createdBy()->associate($user);

            if (!$company) {
                throw new \LogicException("'company' is required when creating a customer");
            }

            $this->company()->associate($company);
        }

        if ($this->isDirty()) {
            $this->updatedBy()->associate($user);
        }

        return $this;
    }

    /**
     * We only want to keep notes that exist in the request so delete any notes in the database that aren't part of the
     * request data.
     *
     * @param Collection $notesData
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function deleteClearedNotes(Collection $notesData): self
    {
        $this
            ->notes()
            ->whereNotIn('id', $this->getNoteIds($notesData))
            ->delete();

        return $this;
    }

    /**
     * Update notes that exist in the database with new request data.
     *
     * @param Collection $notesData
     * @param User       $updatedBy
     * @return $this
     */
    public function updateExistingNotes(Collection $notesData, User $updatedBy): self
    {
        $this->notes()->saveMany(
            $this
                ->notes
                ->whereIn('id', $this->getNoteIds($notesData))
                ->map(function (CustomerNote $note) use ($notesData, $updatedBy): CustomerNote {
                    $note->hydrateFromRequest((array) $notesData->firstWhere('data.id', $note->id), $updatedBy);

                    return $note;
                })
        );

        return $this;
    }

    /**
     * Create new notes from the request data that don't yet exist in the database.
     *
     * @param Collection $notesData
     * @param User       $updatedBy
     * @param Company    $company
     * @return $this
     */
    public function createNewNotes(Collection $notesData, User $updatedBy, Company $company): self
    {
        $this->notes()->saveMany(
            $notesData
                ->whereNotIn('data.id', $this->notes->pluck('id'))
                ->map(function (array $requestNote) use ($updatedBy, $company): CustomerNote {
                    $note = new CustomerNote;
                    $note->hydrateFromRequest($requestNote, $updatedBy, $company);

                    return $note;
                })
        );

        return $this;
    }

    /**
     * @param Collection $notesData
     * @return string[]
     */
    private function getNoteIds(Collection $notesData): array
    {
        return $notesData
            ->filter(function (array $notesData): bool {
                return isset($notesData['data']['id']);
            })
            ->pluck('data.id')
            ->all();
    }
}

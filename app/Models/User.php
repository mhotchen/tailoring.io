<?php
namespace App\Models;

use Hash;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * @property string                                                                                                         $id
 * @property string                                                                                                         $email
 * @property string                                                                                                         $password
 * @property string|null                                                                                                    $email_verification
 * @property string                                                                                                         $status
 * @property \Carbon\Carbon|null                                                                                            $created_at
 * @property \Carbon\Carbon|null                                                                                            $updated_at
 * @property-read bool                                                                                                      $is_active
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[]                                       $clients
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[]                                        $tokens
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[]                                            $companies
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStatus($value)
 * @mixin \Eloquent
 */
final class User extends Authenticatable
{
    public const STATUS_AWAITING_EMAIL_VERIFICATION = 'AWAITING_EMAIL_VERIFICATION';
    public const STATUS_AWAITING_PASSWORD_RESET = 'AWAITING_PASSWORD_RESET';
    public const STATUS_ACTIVE = 'ACTIVE';

    use HasApiTokens, Notifiable, GeneratesUniqueUuid;

    /** @var array */
    protected $fillable = ['email', 'password'];

    /** @var array */
    protected $hidden = ['password'];

    /** @var array */
    protected $casts = ['id' => 'string'];


    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function worksFor(Company $company): bool
    {
        return $this->companies->containsStrict('id', $company->id);
    }

    /**
     * @param array $validatedRequestPayload
     * @return self
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \InvalidArgumentException
     */
    public static function fromRequest(array $validatedRequestPayload): self
    {
        $user = new self;
        $user->id = static::uniqueUuid();
        $user->email = $validatedRequestPayload['data']['email'];
        $user->email_verification = static::uniqueUuid('email_verification');
        $user->password = Hash::make($validatedRequestPayload['data']['password']);
        $user->status = self::STATUS_AWAITING_EMAIL_VERIFICATION;

        return $user;
    }
}

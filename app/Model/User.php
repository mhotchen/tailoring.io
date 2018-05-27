<?php
namespace App\Model;

use Hash;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string|null $email_verification
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\User whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Model\Company[] $companies
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\User whereEmailVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\User whereStatus($value)
 * @mixin \Eloquent
 */
final class User extends Authenticatable
{
    public const STATUS_AWAITING_EMAIL_VERIFICATION = 'AWAITING_EMAIL_VERIFICATION';
    public const STATUS_AWAITING_PASSWORD_RESET = 'AWAITING_PASSWORD_RESET';
    public const STATUS_ACTIVE = 'ACTIVE';

    use HasApiTokens, Notifiable;

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

    /**
     * @param array $validatedRequestPayload
     * @return self
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \InvalidArgumentException
     */
    public static function fromRequest(array $validatedRequestPayload): self
    {
        $user = new self;
        $user->id = Uuid::uuid4();
        $user->email = $validatedRequestPayload['data']['email'];
        $user->email_verification = Uuid::uuid4();
        $user->password = Hash::make($validatedRequestPayload['data']['password']);
        $user->status = self::STATUS_AWAITING_EMAIL_VERIFICATION;

        return $user;
    }
}

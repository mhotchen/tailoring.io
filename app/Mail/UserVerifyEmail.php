<?php

namespace App\Mail;

use App\Model\User;
use App\Spa\UrlGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

final class UserVerifyEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var UrlGenerator */
    private $linkGenerator;

    /** @var User */
    private $user;

    public function __construct(User $user, UrlGenerator $linkGenerator)
    {
        $this->linkGenerator = $linkGenerator;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('emails.users.verify')
            ->with(
                'verifyLink',
                $this->linkGenerator->generate('/users/verifications/' . $this->user->email_verification)
            );
    }
}

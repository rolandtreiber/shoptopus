<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * @property boolean $anonimize
 * @property string $originalEmail
 * @property string $originalFirstName
 * @property string $originalLastName
 * @property string|null $originalPhone
 * @property string|null $emailReplacement
 * @property string|null $firstNameReplacement
 * @property string|null $lastNameReplacement
 * @property string|null $phoneReplacement
 */
class UserAccountSuccessfullyDeactivatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param bool $anonimize
     * @param string $originalEmail
     * @param string $originalFirstName
     * @param string $originalLastName
     * @param string|null $originalPhone
     * @param string|null $emailReplacement
     * @param string|null $firstNameReplacement
     * @param string|null $lastNameReplacement
     * @param string|null $phoneReplacement
     */
    public function __construct(private readonly bool                 $anonimize,
                                private readonly string               $originalEmail,
                                private readonly string               $originalFirstName,
                                private readonly string               $originalLastName,
                                private readonly string|null          $originalPhone,
                                private readonly string|null          $emailReplacement = null,
                                private readonly string|null          $firstNameReplacement = null,
                                private readonly string|null          $lastNameReplacement = null,
                                private readonly string|null          $phoneReplacement = null)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your account has been successfully deactivated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.user-account-deletion-complete',
            with: [
                "anonimize" => $this->anonimize,
                "originalEmail" => $this->originalEmail,
                "originalFirstName" => $this->originalFirstName,
                "originalLastName" => $this->originalLastName,
                "originalPhone" => $this->originalPhone,
                "emailReplacement" => $this->emailReplacement,
                "firstNameReplacement" => $this->firstNameReplacement,
                "lastNameReplacement" => $this->lastNameReplacement,
                "phoneReplacement" => $this->phoneReplacement,
            ]
        );
    }
}

<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class GenericAdminEmail extends ArchiveableEmail
{
    use Queueable, SerializesModels;

    public $subject;

    public $files;

    public $recipient;

    public $body;

    /**
     * Create a new message instance.
     */
    public function __construct(Request $request, $recipient)
    {
        $user = User::where('email', $recipient)->first();
        $subject = $request->subject;
        $body = $request->body;
        if ($user) {
            $body = str_replace('{{name}}', $user->name, $request->body);
            $subject = str_replace('{{name}}', $user->name, $request->subject);
            $body = str_replace('{{reference}}', $user->client_ref, $body);
            $subject = str_replace('{{reference}}', $user->client_ref, $subject);
        }

        parent::__construct($subject, $body, $recipient, 'email.admin-generic');
        $this->subject = $subject;
        $this->recipient = $recipient;
        $this->files = $request->allFiles();
        $this->body = $body;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $email = $this->view('email.admin-generic', ['body' => $this->body])->subject($this->subject);
        if (array_key_exists('files', $this->files)) {
            foreach ($this->files['files'] as $file) {
                $email->attach($file->getRealPath(), [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                ]);
            }
        }

        return $email;
    }
}

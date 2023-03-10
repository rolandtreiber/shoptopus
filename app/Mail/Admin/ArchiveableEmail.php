<?php

namespace App\Mail\Admin;

use App\Models\EmailArchive;
use Illuminate\Mail\Mailable;

class ArchiveableEmail extends Mailable
{
    public function __construct($subject, $body, $recipient, $templateId)
    {
        $archive = new EmailArchive();
        $archive->subject = $subject;
        $archive->content = $body;
        $archive->template_id = $templateId;
        $archive->address = $recipient;
        $archive->save();
    }
}

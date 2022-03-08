<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $subject
 * @property string $content
 * @property string $template_id
 * @property string $address
 */
class EmailArchive extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'address', 'subject', 'template_id', 'content'];
}

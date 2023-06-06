<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = "password_reset_tokens";

    use HasFactory;

    protected $fillable = [
        'email',
        'token',
    ];
}

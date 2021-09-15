<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Rating extends Model implements Auditable
{
    use HasFactory;
    use HasFiles;
    use \OwenIt\Auditing\Auditable;
}

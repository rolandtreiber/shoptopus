<?php

namespace App\Repositories\Admin\Note;

use App\Models\Invoice;
use App\Models\Note;
use App\Models\Order;

interface NoteRepositoryInterface
{
    public function create($data): Note;
}

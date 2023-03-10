<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;

/**
 * @property string $subject
 * @property string $content
 * @property string $template_id
 * @property string $address
 */
class EmailArchive extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, \OwenIt\Auditing\Auditable, HasExportable;

    protected $fillable = ['subject', 'address', 'template_id', 'content'];
}

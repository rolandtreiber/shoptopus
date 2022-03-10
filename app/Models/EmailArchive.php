<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasExportable;

    protected $fillable = ['subject', 'address', 'subject', 'template_id', 'content'];
}

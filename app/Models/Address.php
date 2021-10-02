<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string $id
 * @property string $name
 * @property string $address_line_1
 * @property string|null $address_line_2
 * @property string $town
 * @property string $post_code
 * @property float|null $lat
 * @property float|null $lon
 */
class Address extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'town',
        'post_code',
        'address_line_1',
        'address_line_2',
        'lat',
        'lon'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'lat' => 'decimal:6',
        'lon' => 'decimal:6'
    ];
}

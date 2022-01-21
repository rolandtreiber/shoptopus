<?php

namespace App\Models;

use App\Models\User;
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
 * @property string $country
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
        'name',
        'address_line_1',
        'address_line_2',
        'town',
        'post_code',
        'country',
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

    /**
     * An address belongs to a user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * @return string[]
     */
    public function getComposite(): array
    {
        $textValue = $this->address_line_1 . ", " . $this->address_line_2 . ", " . $this->town . ", " . $this->post_code;
        $url = "https://www.google.com/maps/@".$this->lat.",".$this->lon.",14z";
        return [
            'text' => $textValue,
            'url' => $url
        ];
    }
}

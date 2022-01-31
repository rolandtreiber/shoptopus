<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model implements Auditable
{
    use HasFactory, SoftDeletes, HasUUID, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address_line_1',
        'town',
        'post_code',
        'country',
        'user_id',
        'name',
        'address_line_2',
        'lat',
        'lon',
        'deleted_at'
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

//    /**
//     * @return string[]
//     */
//    public function getComposite(): array
//    {
//        $textValue = $this->address_line_1 . ", " . $this->address_line_2 . ", " . $this->town . ", " . $this->post_code;
//        $url = "https://www.google.com/maps/@".$this->lat.",".$this->lon.",14z";
//        return [
//            'text' => $textValue,
//            'url' => $url
//        ];
//    }
}

<?php

namespace App\Models\PaymentProvider;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'enabled', 'test_mode',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
        'test_mode' => 'boolean',
    ];

    /**
     * A payment provider has many config.
     */
    public function payment_provider_configs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentProviderConfig::class);
    }
}

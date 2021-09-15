<?php

namespace App\Models;

use App\Traits\HasFile;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static find(int|string|null $getUserIdentifier)
 * @method static count()
 * @property mixed $id
 */
class User extends Authenticatable implements Auditable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasFile;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'role_id',
        'email',
        'email_verified_at',
        'password',
        'client_ref',
        'language_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'role_id' => 'integer',
        'email_verified_at' => 'timestamp',
        'language_id' => 'integer',
    ];


    public function paymentSources()
    {
        return $this->hasMany(\App\PaymentSource::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Payment::class);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function cart()
    {
        return $this->hasOne(\App\Cart::class);
    }

    public function language()
    {
        return $this->hasOne(\App\Language::class);
    }

}

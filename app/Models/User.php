<?php

namespace App\Models;

use App\Http\Requests\ListRequest;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static find(int|string|null $getUserIdentifier)
 * @method static count()
 * @method static role(mixed $role)
 * @method filtered(array $array, ListRequest $request)
 * @method static systemUsers()
 * @property string $id
 * @property string|null $email
 * @property string|null $email_verified_at
 * @property string|null $avatar
 * @property mixed|string $password
 * @property string $first_name
 * @property string $last_name
 * @property string|null $prefix
 * @property mixed|string $name
 * @property mixed|string $initials
 * @property string $client_ref
 * @property Address[] $addresses
 * @property Order[] $orders
 * @property Payment[] $payments
 * @property PaymentSource[] $paymentSources
 * @property Cart|null $cart
 */
class User extends Authenticatable implements Auditable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasFile;
    use HasUUID;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'prefix',
        'email',
        'email_verified_at',
        'password',
        'client_ref',
        'language_id',
        'avatar',
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
        'email_verified_at' => 'timestamp',
        'avatar' => 'object'
    ];

    public function scopeSystemUsers($query)
    {
        $roleIds = Role::whereNotIn('name', ['customer'])->pluck('id');

        foreach ($roleIds as $roleId) {
            $query->orWhereHas('roles', function($query) use ($roleId){
                $query->where('roles.id', $roleId);
            });
        }

        return $query;
    }

    public function scopeFiltered($query, $filters, ListRequest $request = null)
    {
        if ($request && $request->filters) {
            foreach ($request->filters as $key => $value) {
                if (json_decode($value) && is_array(json_decode($value))) {
                    $decodedValue = json_decode($value);
                    $filters[] = [$key, $decodedValue[0], $decodedValue[1]];
                } else {
                    $filters[] = [$key, $value];
                }
            }
        }
        foreach ($filters as $filter) {
            if (sizeof($filter) === 2) {
                $query->where($filter[0], $filter[1]);
            }
            if (sizeof($filter) === 3) {
                $query->where($filter[0], $filter[1], $filter[2]);
            }
        }
        return $query;
    }

    /**
     * @return HasMany
     */
    public function paymentSources(): HasMany
    {
        return $this->hasMany(PaymentSource::class);
    }

    /**
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * @return HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

}

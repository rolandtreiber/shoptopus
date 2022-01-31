<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use App\Traits\Searchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use Notifiable;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasFile;
    use HasUUID;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'prefix',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'client_ref',
        'language_id',
        'avatar',
        'is_favorite'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'email_verified_at' => 'datetime',
        'avatar' => 'object',
        'is_favorite' => 'boolean'
    ];

    /**
     * Get the social accounts of the user.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url = config('app.frontend_url_public') . '/reset-password?token=' . $token;

        $this->notify(new ResetPasswordNotification($url));
    }





    public function scopeSystemUsers($query)
    {
        $roleIds = Role::whereNotIn('name', ['customer'])->pluck('id');

        foreach ($roleIds as $roleId) {
            $query->orWhereHas('roles', function ($query) use ($roleId) {
                $query->where('roles.id', $roleId);
            });
        }

        return $query;
    }

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'returning':
                $query->has('orders', '>', 1);
                break;
            case 'ordered_recently':
                $query->whereHas('orders', function ($q) {
                    $q->where('created_at', '>=', Carbon::now()->subMonth());
                });
                break;
        }
    }


    public function scopeCustomers($query)
    {
        $query->role('customer');
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
     * @return HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }
}

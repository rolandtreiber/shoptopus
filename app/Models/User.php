<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Permission\Models\Role;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements Auditable, Exportable
{
    use Notifiable, HasApiTokens, HasFactory, HasRoles, HasFile, HasUUID, SoftDeletes, \OwenIt\Auditing\Auditable, Searchable, HasSlug, HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['first_name', 'last_name'])
            ->saveSlugsTo('slug');
    }

    /**
     * @var array
     */
    protected $exportableFields = [
        'slug',
        'name',
        'prefix',
        'email',
        'phone',
        'email_verified_at',
        'client_ref',
        'role_names'
    ];

    protected $exportableRelationships = [
        'addresses',
        'paymentSources',
        'payments',
        'orders'
    ];

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
        'avatar',
        'is_favorite',
        'deleted_at'
    ];

    protected $appends = ['role_names'];

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

    public function getRoleNamesAttribute()
    {
        return implode(', ', $this->getRoleNames()->toArray());
    }

    /**
     * Get the social accounts of the user.
     * @return HasMany
     */
    public function addresses() : HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the orders of the user.
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the payment sources of the user.
     * @return HasMany
     */
    public function payment_sources(): HasMany
    {
        return $this->hasMany(PaymentSource::class);
    }

    /**
     * Get the cart for the user.
     * @return HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
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
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}

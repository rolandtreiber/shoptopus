<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use App\Traits\Searchable;
use Google\Collection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Traits\NotificationTrait;
use App\Notifications\VerifyEmail;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $prefix
 * @property string $email
 * @property string $phone
 * @property Carbon $email_verified_at
 * @property string $password
 * @property string $client_ref
 * @property string $avatar
 * @property int  $is_favorite
 * @property Carbon $deleted_at
 * @property Collection $addresses
 * @property Collection $orders
 * @property Collection $social_accounts
 * @property Collection $payment_sources
 * @property Collection $payments
 * @property Cart $cart
 */
class User extends Authenticatable implements Auditable, Exportable
{
    use Notifiable,
        NotificationTrait,
        HasApiTokens,
        HasFactory,
        HasRoles,
        HasFile,
        HasUUID,
        SoftDeletes,
        \OwenIt\Auditing\Auditable,
        Searchable,
        HasSlug,
        HasExportable;

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
        'payment_sources',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function social_accounts() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialAccount::class);
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
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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

    /**
     * Send an email verification notification to the user.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
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
}

<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmail;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use App\Traits\NotificationTrait;
use App\Traits\Searchable;
use Carbon\Carbon;
use Google\Collection;
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
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

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
 * @property mixed $unreadNotifications
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
    public function getSlugOptions(): SlugOptions
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
        'role_names',
    ];

    protected $exportableRelationships = [
        'addresses',
        'payment_sources',
        'payments',
        'orders',
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
        'deleted_at',
    ];

    protected $appends = ['role_names'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
        'is_favorite' => 'boolean',
    ];

    public function getRoleNamesAttribute()
    {
        return implode(', ', $this->getRoleNames()->toArray());
    }

    /**
     * Get the social accounts of the user.
     */
    public function social_accounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the social accounts of the user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the orders of the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the payment sources of the user.
     */
    public function payment_sources(): HasMany
    {
        return $this->hasMany(PaymentSource::class);
    }

    /**
     * Get the cart for the user.
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
        $url = config('app.frontend_url_public').'/reset-password?token='.$token;

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

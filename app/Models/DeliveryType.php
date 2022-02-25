<?php

namespace App\Models;

use App\Traits\HasUUID;
use App\Enums\OrderStatuses;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryType extends SearchableModel
{
    use HasFactory, HasUUID, HasTranslations, SoftDeletes;

    public array $translatable = [
        'name', 'description'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'enabled',
        'enabled_by_default_on_creation'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'price' => 'float',
        'enabled' => 'boolean',
        'enabled_by_default_on_creation' => 'boolean'
    ];

    /**
     * @return HasMany
     */
    public function delivery_rules(): HasMany
    {
        return $this->hasMany(DeliveryRule::class);
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return int
     */
    public function getOrderCount(): int
    {
        return DB::table('orders')
            ->where('delivery_type_id', $this->id)
            ->whereIn('status', [
                OrderStatuses::Paid,
                OrderStatuses::Processing,
                OrderStatuses::Completed,
                OrderStatuses::OnHold
            ])
            ->count();
    }

    /**
     * @return int
     */
    public function getTotalRevenue(): int
    {
        return DB::table('orders')
            ->select('delivery_cost')
            ->where('delivery_type_id', $this->id)
            ->whereIn('status', [
                OrderStatuses::Paid,
                OrderStatuses::Processing,
                OrderStatuses::Completed,
                OrderStatuses::OnHold])
            ->sum('delivery_cost');
    }

}

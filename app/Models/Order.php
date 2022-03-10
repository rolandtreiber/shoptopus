<?php

namespace App\Models;

use App\Enums\DiscountTypes;
use App\Enums\OrderStatuses;
use App\Helpers\GeneralHelper;
use App\Traits\HasEventLogs;
use App\Traits\HasUUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @method static find(int $selectedCartId)
 * @property int|mixed $user_id
 * @property int|mixed $delivery_type_id
 * @property int $status
 * @property mixed $address_id
 * @property VoucherCode|null $voucherCode
 * @property string $id
 * @property string|null $voucher_code_id
 * @property int $originalPrice
 * @property float $original_price
 * @property float $total_price
 * @property mixed $created_at
 * @property Address $address
 * @property User $user
 * @property Product[] $products
 * @property mixed $pivot
 * @property float $delivery
 * @property Payment[] $payments
 * @property Carbon $updated_at
 * @property float $total_discount
 * @property float $subtotal
 */
class Order extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasUUID;
    use SoftDeletes;
    use HasEventLogs;
    use HasSlug;
    use \OwenIt\Auditing\Auditable;
    use HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['user.name', 'address.town'])
            ->saveSlugsTo('slug');
    }

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'status',
        'original_price',
        'subtotal',
        'total_price',
        'total_discount'
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'user',
        'address',
        'products',
        'payments',
        'voucherCode',
        'deliveryType'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'original_price',
        'subtotal',
        'total_price',
        'total_discount'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'status' => 'integer',
        'original_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_price' => 'decimal:2',
        'total_discount' => 'decimal:2'
    ];

    public function scopeSearch($query, $search)
    {
        $query->whereHas('user', function($q) use($search) {
            $q->where('name', 'like', '%'.$search.'%');
        });
    }

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'paid':
                $query->where('status', OrderStatuses::Paid);
                break;
            case 'processing':
                $query->where('status', OrderStatuses::Processing);
                break;
            case 'in_transit':
                $query->where('status', OrderStatuses::InTransit);
                break;
            case 'completed':
                $query->where('status', OrderStatuses::Completed);
                break;
            case 'on_hold':
                $query->where('status', OrderStatuses::OnHold);
                break;
            case 'cancelled':
                $query->where('status', OrderStatuses::Cancelled);
                break;
        }
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot(['id', 'name', 'amount', 'full_price', 'original_unit_price', 'unit_price', 'final_price', 'unit_discount', 'total_discount'])->using(OrderProduct::class);
    }

    /**
     * @return MorphMany
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * @return BelongsTo
     */
    public function voucherCode(): BelongsTo
    {
        return $this->belongsTo(VoucherCode::class);
    }

    /**
     * @return BelongsTo
     */
    public function deliveryType(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }

    public function recalculatePrices()
    {
        $dispatcher = Order::getEventDispatcher();
        Order::unsetEventDispatcher();

        $subTotal = DB::table('order_product')->where('order_id', $this->id)->sum('final_price');
        $originalPrice = DB::table('order_product')->where('order_id', $this->id)->sum('full_price');
        $discount = DB::table('order_product')->where('order_id', $this->id)->sum('total_discount');
        $total = $subTotal + $this->delivery;

        $this->total_discount = $discount;
        $this->subtotal = $subTotal;
        $this->original_price = $originalPrice;
        $voucherCode = $this->voucherCode;
        if ($voucherCode) {
            switch (config('shoptopus.voucher_code_basis')) {
                case 'full_price':
                    $basis = $originalPrice;
                    break;
                case 'final_price':
                    $basis = $total;
                    break;
                default:
                    $basis = $total;
            }
            $this->total_price = GeneralHelper::getDiscountedValue($voucherCode->type, $voucherCode->amount, $basis);
        } else {
            $this->total_price = $total;
        }
        $this->save();
        Order::setEventDispatcher($dispatcher);
    }
}

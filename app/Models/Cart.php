<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;

/**
 * @property mixed $user_id
 *
 * @method static count()
 * @method static find(int $selectedCartId)
 *
 * @property mixed $updated_at
 */
class Cart extends Model implements Auditable, Exportable
{
    use HasFactory, HasUUID, \OwenIt\Auditing\Auditable, HasExportable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ip_address',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot([
            'quantity',
            'product_variant_id',
        ]);
    }

    public static function quantityValidationRule($productId): array
    {
        return ['required', 'integer', 'min:1', function ($attribute, $value, $fail) use ($productId) {
            $productQuery = DB::table('products')
                ->whereNull('deleted_at')
                ->where('id', $productId);

            if (! $productQuery->exists()) {
                $fail('Product is unavailable.');
            } else {
                $stock = (int) $productQuery->select(['stock'])->first()['stock'];

                if ($stock < $value) {
                    if ($stock === 0) {
                        $fail('Out of stock.');
                    } elseif ($stock === 1) {
                        $fail('Only 1 left.');
                    } else {
                        $fail('Only '.$stock.' left.');
                    }
                }
            }
        }];
    }
}

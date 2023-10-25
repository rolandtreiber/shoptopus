<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use App\Models\DiscountRule;
use App\Models\FileContent;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\PaymentProvider\PaymentProvider;
use App\Models\PaymentSource;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;
use App\Services\Remote\Translations\TranslationService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use stdClass;

class TestStore1Seeder extends Seeder
{
    private TranslationService $translationService;

    public function __construct(
        TranslationService $translationService
    )
    {
        $this->translationService = $translationService;
    }

    private function importPivotRecords(string $table, array $data, bool $hasId = false): bool
    {
        foreach ($data as $row) {
            array_walk($row, function (&$a, $b) use ($row) {
                if (str_contains($b, "_id") && $b !== "parent_id" && $b !== "model_id" && $b !== "role_id" && $b !== "permission_id") {
                    if ($a !== null) {
                        $modelClass = "App\\Models\\" . str_replace("Id", "", str_replace(" ", "", ucwords(str_replace("_", " ", $b))));
                        $model = ($modelClass::where('slug', $a)->first());
                        if ($model) {
                            $a = $model->id;
                        } else {
                            dd($a, $b, $modelClass);
                        }
                    }
                }

                if (str_contains($b, "model_id")) {
                    $type = str_replace("_id", "_type", $b);
                    $modelClass = $row[$type];
                    $model = ($modelClass::where('slug', $a)->first());
                    if ($model) {
                        $a = $model->id;
                    } else {
                        dd($a, $b, $modelClass);
                    }
                }


            });
            if ($hasId) {
                $row['id'] = (string)Str::orderedUuid();
            }
            try {
                DB::table($table)->insert([$row]);
            } catch (QueryException $exception) {
                dd($row);
            }
        }
        return true;
    }

    private function importRecords(string $model, array $data, $needsIdPopulated = true): bool
    {
        $availableLanguages = array_keys(array_diff_key(config('app.locales_supported'), array_flip(["en"])));
        foreach ($data as $row) {
            if (array_key_exists('children', $row)) {
                $sanitised = array_diff_key($row, array_flip(["children"]));
            } else {
                $sanitised = $row;
            }
            if ($needsIdPopulated) {
                $sanitised['id'] = (string)Str::orderedUuid();
            }
            $sanitised['created_at'] = Carbon::now();
            array_walk($sanitised, function (&$a, $b) use ($availableLanguages, $row) {
                $value = $a;
                if (is_string($a)) {
                    if (str_contains($a, "(T)")) {
                        $translatable = str_replace("(T)", "", $a);
                        if (config('app.auto_translate_test_data') === true) {
                            $translatables = $this->translationService->translate($translatable, $availableLanguages);
                        } else {
                            $translatables = [];
                        }
                        $translatables['en'] = $translatable;
                        $a = $translatables;
                    }

                    if (str_contains($b, "_id") && $b !== "parent_id" && $b !== "fileable_id" && $b !== "source_id" && $b !== "payable_id" && $b !== "stripe_user_id" && $b !== "payment_method_id") {
                        $modelClass = "App\\Models\\" . str_replace("Id", "", str_replace(" ", "", ucwords(str_replace("_", " ", $b))));
                        $model = ($modelClass::where('slug', $a)->first());
                        if ($model) {
                            $a = $model->id;
                        } else {
                            dd($a, $b, $modelClass);
                        }
                    }

                    if (str_contains($b, "able_id")) {
                        $type = str_replace("_id", "_type", $b);
                        $modelClass = $row[$type];
                        $model = ($modelClass::where('slug', $a)->first());
                        if ($model) {
                            $a = $model->id;
                        } else {
                            dd($a, $b, $modelClass);
                        }
                    }

                    if (str_contains($b, "password")) {
                        $a = bcrypt($a);
                    }

                    if (str_contains($b, "button_url")) {
                        $a = config('app.frontend_url_public') . $a;
                    } else if (str_contains($b, "url")) {
                        $a = config('app.url') . $a;
                    }

                } elseif (is_array($a)) {
                    if ($b === "attribute_options") {
                        $a = array_map(function ($item) {
                            $attributeOption = DB::table('product_attribute_options')->where('slug', $item)->select('id')->first();
                            if ($attributeOption) {
                                return $attributeOption['id'];
                            } else {
                                dd($item);
                            }
                        }, $a);
                    } else {
                        array_walk($a, function (&$val, $key) {
                            if ($key === "url") {
                                $val = config('app.url') . $val;
                            }
                        });
                    }
                }
            });
            $record = (new $model());
            $newRecord = $record->fill($sanitised);
            $newRecord->save();
            if (array_key_exists('children', $row)) {
                $children = array_map(function ($item) use ($newRecord) {
                    $item['parent_id'] = $newRecord->id;
                    return $item;
                }, $row['children']);
                $this->importRecords($model, $children);
            }
        }
        return true;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mail::fake();
        NotificationFacade::fake();

        // Import roles
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/roles.json");
        $this->importRecords(Role::class, json_decode($data, true), false);

        // Import permissions
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/permissions.json");
        $this->importRecords(Permission::class, json_decode($data, true), false);

        // Import payment providers
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/payment-providers.json");
        $this->importRecords(PaymentProvider::class, json_decode($data, true), false);

        // Import role has permissions (pivot)
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/role-has-permissions.json");
        $this->importPivotRecords('role_has_permissions', json_decode($data, true));

        // Import users
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/users.json");
        $this->importRecords(User::class, json_decode($data, true));

        // Import product product categories (pivot)
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/model-has-roles.json");
        $this->importPivotRecords('model_has_roles', json_decode($data, true));

        // Import product categories
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-categories.json");
        $this->importRecords(ProductCategory::class, json_decode($data, true));

        // Import product attributes
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-attributes.json");
        $this->importRecords(ProductAttribute::class, json_decode($data, true));

        // Import product attribute options
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-attribute-options.json");
        $this->importRecords(ProductAttributeOption::class, json_decode($data, true));

        // Import product tags
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-tags.json");
        $this->importRecords(ProductTag::class, json_decode($data, true));

        // Import voucher codes
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/voucher-codes.json");
        $this->importRecords(VoucherCode::class, json_decode($data, true));

        // Import delivery types
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/delivery-types.json");
        $this->importRecords(DeliveryType::class, json_decode($data, true));

        // Import delivery rules
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/delivery-rules.json");
        $this->importRecords(DeliveryRule::class, json_decode($data, true));

        // Import products
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/products.json");
        $this->importRecords(Product::class, json_decode($data, true));

        // Import product product categories (pivot)
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/products-product-categories.json");
        $this->importPivotRecords('product_product_category', json_decode($data, true));

        // Import product_variants
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-variants.json");
        $this->importRecords(ProductVariant::class, json_decode($data, true));

        // Import product attribute option product variant
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-attribute-product-variants.json");
        $this->importPivotRecords('product_attribute_product_variant', json_decode($data, true), true);

        // Import discount rules
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/discount-rules.json");
        $this->importRecords(DiscountRule::class, json_decode($data, true));

        // Import product attribute option product variant
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/discount-rule-product-category.json");
        $this->importPivotRecords('discount_rule_product_category', json_decode($data, true));

        // Import banners
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/banners.json");
        $this->importRecords(Banner::class, json_decode($data, true));

        // Import file contents
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/file-contents.json");
        $this->importRecords(FileContent::class, json_decode($data, true));

        // Import addresses
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/addresses.json");
        $this->importRecords(Address::class, json_decode($data, true));

        // Import payment sources
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/payment-sources.json");
        $this->importRecords(PaymentSource::class, json_decode($data, true));

        // Import orders
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/orders.json");
        $this->importRecords(Order::class, json_decode($data, true));

        // Import order products
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/order-products.json");
        $this->importRecords(OrderProduct::class, json_decode($data, true));

        // Import payments
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/payments.json");
        $this->importRecords(Payment::class, json_decode($data, true));

        // Import carts
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/carts.json");
        $this->importRecords(Cart::class, json_decode($data, true));

        // Import cart products
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/cart-products.json");
        $this->importPivotRecords('cart_product', json_decode($data, true));

    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'status',
        'purchase_count',
        'stock',
        'backup_stock',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'price' => 'decimal',
        'status' => 'integer',
        'purchase_count' => 'integer',
        'stock' => 'integer',
        'backup_stock' => 'integer',
    ];


    public function productTags()
    {
        return $this->hasMany(\App\ProductTag::class);
    }

    public function productCategories()
    {
        return $this->hasMany(\App\ProductCategory::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(\App\ProductAttribute::class);
    }

    public function productVariants()
    {
        return $this->hasMany(\App\ProductVariant::class);
    }
}

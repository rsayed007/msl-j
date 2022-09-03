<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function variant()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function variant_price()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_id')->with(
            'variant_one',
            'variant_two',
            'variant_three'
        );
    }

    public function product_image()
    {
        return $this->hasOne(ProductImage::class);
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class ProductVariantService extends Service
{
    public function __construct()
    {
    }

    public function modelVariant()
    {
        return new ProductVariant();
    }

    public function storeVariant(array $productVariant, int $productId)
    {
        $variants = array();
        foreach ($productVariant as $key => $pv) {
            $tags = $pv["tags"];
            $optionId = $pv["option"];

            foreach ($tags as $tag) {
                $variants[$tag][$key] = ProductVariant::create([
                    "variant" => $tag,
                    "variant_id" => $optionId,
                    "product_id" => $productId
                ]);
            }
        }

        return $variants;
    }
}

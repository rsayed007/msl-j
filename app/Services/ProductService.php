<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Variant;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariantPrice;

class ProductService extends Service
{
    protected $productVariantService;

    public function __construct(
        ProductVariantService $productVariantService
    ) {
        $this->productVariantService = $productVariantService;
    }


    public function getAllProduct(int $limit = 10, array $requestData)
    {
        $title = $requestData["title"] ?? "";
        $priceForm = $requestData['price_from'] ?? "";
        $priceTo = $requestData['price_to'] ?? "";
        $variant = $requestData['variant'] ?? "";
        $date = $requestData["date"] ?? "";

        $product = Product::with('variant_price')->orWhereHas('variant_price', function ($query) use ($priceForm, $priceTo, $variant) {

            if ($priceForm != '') {
                $query->where("price", '>=', $priceForm);
            }
            if ($priceTo != '') {
                $query->where("price", '<=', $priceTo);
            }
            if ($variant != '') {
                $var = ProductVariant::where('variant', $variant)->pluck('id')->toArray();
                $var = implode(",", $var);

                $query->where(function ($q) use ($var) {
                    $queryString = "product_variant_one in ($var) or product_variant_two in ($var) or product_variant_three in ($var)";
                    $q->whereRaw($queryString);
                });
            }
        });

        if ($title != '') {
            $product = $product->where('title', 'like', '%' . $title . '%');
        }

        if ($date != '') {
            $product = $product->whereDate("created_at", $date);
        }

        return $product->latest()->paginate($limit);
    }

    public function storeProduct(array $inputs)
    {

        DB::beginTransaction();
        try {
            $product = Product::create([
                "title" => $inputs["title"],
                "sku" => $inputs["sku"],
                "description" => $inputs["description"]
            ]);

            $variants = $this->productVariantService->storeVariant($inputs["product_variant"], $product->id);

            $variantPrice = $this->processProductVariantPrice($inputs["product_variant_prices"], $variants, $product->id);

            $priceSave =  ProductVariantPrice::insert($variantPrice);

            DB::commit();
            return $priceSave;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function processProductVariantPrice($productVariantPrices, $variants, $productId)
    {
        $variantData = [];
        foreach ($productVariantPrices as $pvp) {
            $price = $pvp["price"];
            $stock = $pvp["stock"];
            $productTitle = explode("/", $pvp["title"]);

            $productVariant1 = "";
            $productVariant2 = "";
            $productVariant3 = "";

            foreach ($productTitle as $k => $title) {
                $title = $productTitle[$k];

                if ($title && isset($variants[$title][$k])) {
                    $id = $variants[$title][$k]->id;
                    if ($k == 0) {
                        $productVariant1 = $id;
                    } elseif ($k == 1) {
                        $productVariant2 = $id;
                    } elseif ($k == 2) {
                        $productVariant3 = $id;
                    }
                }
            }

            $tempData = [
                "product_variant_one" => $productVariant1,
                "product_variant_two" => $productVariant2,
                "product_variant_three" => $productVariant3,
                "price" => $price,
                "stock" => $stock,
                "product_id" => $productId,
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d")
            ];

            array_push($variantData, $tempData);
        }

        return $variantData;
    }
}

<?php

namespace App\Services\Products;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductService
{
    /**
     * @param int $userId
     * @param array $productData
     *
     * @return Product|Builder|Model
     */
    public function createProduct(int $userId, array $productData): Product|Builder|Model
    {
        $quantity = $productData['quantity_available'];
        unset($productData['quantity_available']);

        return Product::query()->create([
           'seller_id' => $userId,
            'amount_available' => $quantity,
            ...$productData,
        ]);
    }

    /**
     * @param Product $product
     * @param array $productData
     *
     * @return Product
     */
    public function updateProduct(Product $product, array $productData): Product
    {
        if (false === empty($productData)) {
            $quantity = $productData['quantity_available'] ?? $product->amount_available;
            unset($productData['quantity_available']);

            $product = $product->fill([
                'amount_available' => $quantity,
                ...$productData,
            ]);

            $product->save();
        }

        return $product;
    }

    /**
     * @param Product $product
     *
     * @return bool
     * @throws Throwable
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            DB::transaction(function () use ($product) {
                $product->purchases()->delete();

                $product->delete();
            });
        } catch (Throwable $ex) {
            app('log')->error($ex->getMessage(), $ex->getTrace());

            return false;
        }

        return true;
    }
}

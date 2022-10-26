<?php

namespace App\Services\Products;

use App\Http\Resources\Products\ProductResource;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductPurchaseService
{
    /**
     * @param int $productId
     * @param User $user
     * @param int $quantity
     *
     * @return ProductPurchase
     * @throws ValidationException
     */
    public function purchase(int $productId, User $user, int $quantity): ProductPurchase
    {
        /** @var Product $product */
        $product = Product::query()->find($productId);

        $this->assertUserHasEnoughDepositBalance($user, $product, $quantity);

        $totalCost = $product->cost * $quantity;

        /** @var ProductPurchase $productPurchase */
        $productPurchase = ProductPurchase::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'unit_cost' => $product->cost,
            'quantity' => $quantity,
        ]);

        // Decrease user deposit
        $user->fill([
            'deposit' => $user->deposit - $totalCost,
        ])->save();

        // Decrease product available quantity
        $product->newQuery()->decrement('amount_available', $quantity);

        return $productPurchase;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function purchaseHistory(User $user): array
    {
        // 1. A list of products purchased
        $products = $user->purchases()->get();

        // 2. Total money spent on purchases
        $totalSum = ProductPurchase::query()->sum(DB::raw('unit_cost * quantity'));

        return [
            'products' => $products->map(function ($item) {
                return [
                    'purchaseCost' => $item->unit_cost,
                    'purchaseQuantity' => $item->quantity,
                    'product' => ProductResource::make($item->product),
                ];
            }),
            'totalSpent' => $totalSum,
        ];
    }

    /**
     * @param User $user
     * @param Product $product
     * @param int $quantity
     *
     * @throws ValidationException
     */
    private function assertUserHasEnoughDepositBalance(User $user, Product $product, int $quantity): void
    {
        if ($user->deposit < ($product->cost * $quantity)) {
            throw ValidationException::withMessages([
                'quantity' => 'Purchase cost exceeds available deposit balance'
            ]);
        }
    }
}

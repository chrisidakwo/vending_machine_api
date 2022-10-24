<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductPurchaseRequest;
use App\Http\Resources\Products\ProductResourceCollection;
use App\Models\Product;
use App\Services\Products\ProductPurchaseService;
use Illuminate\Http\JsonResponse;

class ProductPurchaseController extends Controller
{
    protected ProductPurchaseService $productPurchaseService;

    /**
     * @param ProductPurchaseService $productPurchaseService
     */
    public function __construct(ProductPurchaseService $productPurchaseService)
    {
        $this->middleware('auth:api');

        $this->productPurchaseService = $productPurchaseService;
    }

    public function purchase(ProductPurchaseRequest $request): JsonResponse
    {
        $this->productPurchaseService->purchase(
            $request->get('product'),
            $request->user(),
            $request->get('quantity')
        );

        $purchaseHistory = $this->productPurchaseService->purchaseHistory($request->user());

        return response()->json([
            'purchases' => ProductResourceCollection::make($purchaseHistory['products']),
            'totalPurchaseAmount' => $purchaseHistory['totalSpent'],
        ]);
    }
}

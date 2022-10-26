<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductPurchaseRequest;
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

    /**
     * @param ProductPurchaseRequest $request
     *
     * @return JsonResponse
     */
    public function purchase(ProductPurchaseRequest $request): JsonResponse
    {
        $this->productPurchaseService->purchase(
            $request->get('product'),
            $request->user(),
            $request->get('quantity')
        );

        $purchaseHistory = $this->productPurchaseService->purchaseHistory($request->user());

        return response()->json([
            'purchases' => $purchaseHistory['products'],
            'totalPurchaseAmount' => (int) $purchaseHistory['totalSpent'],
            'remainingDeposit' => $request->user()->refresh()->deposit,
        ]);
    }
}

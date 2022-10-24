<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\DeleteProductRequest;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\Products\ProductResource;
use App\Models\Product;
use App\Services\Products\ProductService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProductController extends Controller
{
    protected ProductService $productService;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->middleware('auth:api');

        $this->productService = $productService;
    }

    /**
     * @param StoreProductRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->user()->id, $request->toArray());

        return response()->json(ProductResource::make($product), Response::HTTP_CREATED);
    }

    /**
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function view(Product $product): JsonResponse
    {
        return response()->json(ProductResource::make($product));
    }

    /**
     * @param UpdateProductRequest $request
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->updateProduct($product, $request->toArray());

        return response()->json(ProductResource::make($product));
    }

    /**
     * @param DeleteProductRequest $request
     * @param Product $product
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function delete(DeleteProductRequest $request, Product $product): JsonResponse
    {
        $result = $this->productService->deleteProduct($product);

        /** @var array<bool, int> $statusMap */
        $statusMap = [
            false => Response::HTTP_INTERNAL_SERVER_ERROR,
            true => Response::HTTP_NO_CONTENT,
        ];

        return response()->json([], $statusMap[$result]);
    }
}

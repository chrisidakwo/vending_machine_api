<?php

namespace App\Http\Resources\Products;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public $resource = Product::class;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'amountAvailable' => $this->amount_available,
            'cost' => $this->cost,
            'productName' => $this->product_name,
            'seller' => [
                'id' => $this->seller->id,
                'username' => $this->seller->username,
            ],
        ];
    }
}

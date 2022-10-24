<?php

namespace App\Http\Requests\Products;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->ownsProduct($this->route('product'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        /** @var Product $product */
        $product = $this->route('product');

        return [
            'product_name' => ['sometimes', 'string', 'min:6', Rule::unique('products')->ignoreModel($product)],
            'cost' => ['sometimes', 'integer', Rule::in(config('vendingmachine.products.price_ranges'))],
            'quantity_available' => ['sometimes', 'integer'],
        ];
    }
}

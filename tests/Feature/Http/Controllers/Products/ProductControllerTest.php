<?php

namespace Tests\Feature\Http\Controllers\Products;

use App\Models\Product;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\IntegrationTestCase;

class ProductControllerTest extends IntegrationTestCase
{
    public function testStoreProductAsABuyer(): void
    {
        $user = User::factory()->buyer()->create();

        $response = $this->actingAs($user)->postJson(route('products.store'), [
            'product_name' => 'Test Product',
            'amount_available' => 10,
            'cost' => 20,
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonPath('message', 'This action is unauthorized.');
    }

    public function testStoreProduct(): void
    {
        $user = User::factory()->seller()->create();

        $response = $this->actingAs($user)->postJson(route('products.store'), [
            'product_name' => 'Test Product',
            'quantity_available' => 10,
            'cost' => 20,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonPath('productName', 'Test Product');
        $response->assertJsonPath('amountAvailable', 10);
        $response->assertJsonPath('cost', 20);
        $response->assertJsonPath('seller.id', 1);
    }

    public function testUpdateProduct(): void
    {
        $user = User::factory()->seller()->create();
        $product = Product::factory(1, [
            'product_name' => 'Test Product 2',
            'cost' => 100,
            'amount_available' => 20,
        ])->forOwner($user)->create()->first();

        $response = $this->actingAs($user)->putJson(route('products.update', [ 'product' => $product->id ]), [
            'cost' => 50,
            'quantity_available' => 15,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('amountAvailable', 15);
        $response->assertJsonPath('cost', 50);
    }

    public function testDeleteProduct(): void
    {
        $user = User::factory()->seller()->create();
        $product = Product::factory(1, [
            'product_name' => 'Test Product 2',
            'cost' => 100,
            'amount_available' => 20,
        ])->forOwner($user)->create()->first();

        $response = $this->actingAs($user)->deleteJson(route('products.delete', [ 'product' => $product->id ]));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}

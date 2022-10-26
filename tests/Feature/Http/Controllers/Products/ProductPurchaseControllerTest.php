<?php

namespace Tests\Feature\Http\Controllers\Products;

use App\Models\Product;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\IntegrationTestCase;

class ProductPurchaseControllerTest extends IntegrationTestCase
{
    public function testBuyProductAsASeller()
    {
        $seller = User::factory()->seller()->create();

        $product = Product::factory(1, [
            'product_name' => 'Test Product 2',
            'cost' => 20,
            'amount_available' => 20,
        ])->forOwner($seller)->create()->first();

        $response = $this->actingAs($seller)->postJson(route('buy'), [
            'product' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonPath('message', 'This action is unauthorized.');
    }

    public function testBuyProduct(): void
    {
        $seller = User::factory()->seller()->create();
        $buyer = User::factory()->buyer()->deposit(100)->create();

        $product = Product::factory(1, [
            'product_name' => 'Test Product 2',
            'cost' => 20,
            'amount_available' => 20,
        ])->forOwner($seller)->create()->first();

        $response = $this->actingAs($buyer)->postJson(route('buy'), [
            'product' => $product->id,
            'quantity' => 2,
        ]);

        self::assertEquals(60, $buyer->refresh()->deposit);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('purchases.0.purchaseQuantity', 2);
        $response->assertJsonPath('purchases.0.purchaseCost', 20);
        $response->assertJsonPath('totalPurchaseAmount', 40);
        $response->assertJsonPath('remainingDeposit', 60);
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\User\Cart;
use App\Models\Drink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * #CRT-01: View Cart
     * User visits the cart page.
     */
    public function test_cart_page_loads_with_items(): void
    {
        $drink = Drink::factory()->create();
        
        Session::put('cart', [
            $drink->id => [
                'name' => $drink->name,
                'price' => $drink->price,
                'quantity' => 1,
                'image_url' => $drink->image_url
            ]
        ]);

        Livewire::test(Cart::class)
            ->assertStatus(200)
            ->assertSee($drink->name);
    }

    /**
     * #CRT-02: Update Quantity
     * User increments/decrements item quantity.
     */
    public function test_cart_update_quantity(): void
    {
        $drink = Drink::factory()->create();
        
        Session::put('cart', [
            $drink->id => [
                'name' => $drink->name,
                'quantity' => 1,
                'price' => 10
            ]
        ]);

        Livewire::test(Cart::class)
            ->call('increment', $drink->id);

        $this->assertEquals(2, session('cart')[$drink->id]['quantity']);

        Livewire::test(Cart::class)
            ->call('decrement', $drink->id);

        $this->assertEquals(1, session('cart')[$drink->id]['quantity']);
    }

    /**
     * #CRT-03: Remove Item
     * User clicks remove on an item.
     */
    public function test_cart_remove_item(): void
    {
        $drink = Drink::factory()->create();
        
        Session::put('cart', [
            $drink->id => [
                'name' => $drink->name,
                'quantity' => 1,
                'price' => 10
            ]
        ]);

        Livewire::test(Cart::class)
            ->call('remove', $drink->id);

        $this->assertEmpty(session('cart'));
    }

    /**
     * #CRT-04: Clear Cart
     * User clicks "Clear Cart".
     */
    public function test_cart_clear(): void
    {
        $drink = Drink::factory()->create();
        
        Session::put('cart', [
            $drink->id => [
                'name' => $drink->name,
                'quantity' => 1,
                'price' => 10
            ]
        ]);

        Livewire::test(Cart::class)
            ->call('clear');

        $this->assertEmpty(session('cart'));
    }
}

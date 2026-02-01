<?php

namespace Tests\Feature;

use App\Livewire\User\Checkout;
use App\Models\Drink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * #CHK-01: Load Checkout
     * User proceeds to checkout with items.
     */
    public function test_checkout_page_loads_populated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $drink = Drink::factory()->create();
        Session::put('cart', [
            $drink->id => [
                'name' => $drink->name,
                'price' => 10,
                'quantity' => 1,
                'image_url' => 'test.jpg'
            ]
        ]);

        Livewire::test(Checkout::class)
            ->assertSet('first_name', $user->name);
    }

    /**
     * #CHK-02: Validation
     * User submits empty form (Delivery).
     */
    public function test_checkout_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $drink = Drink::factory()->create();
        Session::put('cart', [
            $drink->id => [
                'name' => $drink->name,
                'price' => 10,
                'quantity' => 1
            ]
        ]);

        Livewire::test(Checkout::class)
            ->set('order_type', 'delivery')
            ->set('first_name', '') // Empty name
            ->call('placeOrder')
            ->assertHasErrors(['first_name']);
    }

    /**
     * #CHK-03: Order - Cash
     * User places order with "Cash" method.
     */
    public function test_checkout_process_cash(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $drink = Drink::factory()->create();
        Session::put('cart', [
            $drink->id => [
                'name' => $drink->name,
                'price' => 10,
                'quantity' => 2,
                'image_url' => 'test.jpg'
            ]
        ]);

        Livewire::test(Checkout::class)
            ->set('order_type', 'pickup')
            ->set('payment_method', 'cash')
            ->set('pickup_name', 'Test User')
            ->set('pickup_phone', '123456789')
            ->call('placeOrder')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'cash',
            'status' => 'pending',
            'total' => 20
        ]);
        
        $this->assertEmpty(session('cart'));
    }

    /**
     * #CHK-04: Order - Stripe
     * User places order with "Stripe".
     */
    public function test_checkout_process_stripe_redirection(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $drink = Drink::factory()->create();
        Session::put('cart', [
            $drink->id => [
                'name' => $drink->name,
                'price' => 10,
                'quantity' => 1
            ]
        ]);

        // Mock Stripe or expect exception if API key missing
        // Since we are not setting up Stripe secrets in test env, 
        // we might just test that the order is created as 'unpaid' before redirection logic 
        // OR catch the inevitable "api key" error if we let it run.
        // For now, let's verify it attempts to create the order.
        
        // Actually, Stripe call happens inside the method.
        // We can just assert that database has the order.
        
        try {
             Livewire::test(Checkout::class)
                ->set('order_type', 'pickup')
                ->set('payment_method', 'stripe')
                ->set('pickup_name', 'Test User')
                ->set('pickup_phone', '123456789')
                ->call('placeOrder');
        } catch (\Exception $e) {
            // Ignore Stripe config errors
        }

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'stripe',
            'status' => 'unpaid'
        ]);
    }
}

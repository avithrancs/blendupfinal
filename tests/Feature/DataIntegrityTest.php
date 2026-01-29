<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Drink;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_item_price_remains_fixed_when_drink_price_changes()
    {
        // 1. Create a Drink with specific price
        $drink = Drink::create([
            'name' => 'Test Coffee',
            'price' => 5.00,
            'category' => 'Coffee',
        ]);

        // 2. Create a User
        $user = User::factory()->create();

        // 3. Create an Order containing this drink at the current price (5.00)
        $order = Order::create([
            'user_id' => $user->id,
            'order_type' => 'pickup',
            'status' => 'paid',
            'total' => 5.00,
            'payment_method' => 'cash',
        ]);

        $orderItem = $order->items()->create([
            'drink_id' => $drink->id,
            'drink_name' => $drink->name,
            'unit_price' => $drink->price, // Snapshotting 5.00
            'quantity' => 1,
        ]);

        // Verify initial state
        $this->assertEquals(5.00, $orderItem->unit_price, 'Order item should store the initial price.');

        // 4. Update the Drink price in the main catalog
        $drink->update(['price' => 10.00]);

        // 5. Refresh the order item from database
        $orderItem->refresh();

        // 6. Assert Data Integrity
        $this->assertEquals(5.00, $orderItem->unit_price, 'Order item price MUST NOT change when drink price changes.');
        $this->assertEquals(10.00, $drink->price, 'Drink catalog price should be updated.');
    }
}

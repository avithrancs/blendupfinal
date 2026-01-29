<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\DrinkResource;
use App\Http\Resources\OrderResource;
use App\Models\Drink;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemApiController extends Controller
{
    public function getDrinks()
    {
        $drinks = Drink::paginate(10);
        return DrinkResource::collection($drinks);
    }

    public function getDrink($id)
    {
        $drink = Drink::findOrFail($id);
        return new DrinkResource($drink);
    }

    public function getUserOrders(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);
        return OrderResource::collection($orders);
    }

    public function createOrder(StoreOrderRequest $request)
    {
        $data = $request->validated();
        
        return DB::transaction(function () use ($data, $request) {
            $total = 0;
            $itemsToCreate = [];

            foreach ($data['items'] as $item) {
                $drink = Drink::findOrFail($item['drink_id']);
                $itemTotal = $drink->price * $item['quantity'];
                $total += $itemTotal;

                $itemsToCreate[] = [
                    'drink_id' => $drink->id,
                    'drink_name' => $drink->name,
                    'unit_price' => $drink->price,
                    'quantity' => $item['quantity'],
                    'customizations' => $item['customizations'] ?? null,
                ];
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_type' => $data['order_type'],
                'status' => 'pending',
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'address_json' => $data['address_json'] ?? null,
            ]);

            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            return new OrderResource($order->load('items'));
        });
    }

    public function deleteDrink($id)
    {
        $drink = Drink::findOrFail($id);
        $drink->delete();

        return response()->json(['message' => 'Drink deleted successfully.'], 200);
    }
}

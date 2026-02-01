<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Drink;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Checkout extends Component
{
    // Form properties
    // Form properties
    public $order_type = 'delivery'; // 'delivery' or 'pickup'
    public $payment_method = 'stripe'; // 'stripe' or 'cash'
    
    // Delivery fields
    public $first_name = '';
    public $last_name = '';
    public $phone = '';
    public $street = '';
    public $city = '';
    public $province = '';
    public $postal = '';
    public $instructions = '';

    // Pickup fields
    public $pickup_name = '';
    public $pickup_phone = '';

    public $cart = [];

    protected $rules = [
        'order_type' => 'required|in:delivery,pickup',
        'payment_method' => 'required|in:stripe,cash',
    ];

    public function mount()
    {
        $this->cart = Session::get('cart', []);
        
        if (empty($this->cart)) {
            return redirect()->route('cart');
        }

        // Prefill user data if available
        $user = auth()->user();
        $this->first_name = $user->name; 
        $this->pickup_name = $user->name;
    }

    public function placeOrder()
    {
        // Validation Logic based on order type
        $rules = $this->rules;

        if ($this->order_type === 'delivery') {
            $rules = array_merge($rules, [
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required|min:7',
                'street' => 'required',
                'city' => 'required',
            ]);
        } else {
            $rules = array_merge($rules, [
                'pickup_name' => 'required',
                'pickup_phone' => 'required',
            ]);
        }

        // Logic for Stripe or Cash
        // No extra validation needed for Stripe at this stage (handled on Stripe's page)
        // No extra validation needed for Cash

        $this->validate($rules);

        // Calculate Totals
        $subtotal = 0;
        foreach ($this->cart as $item) {
             $subtotal += $item['price'] * $item['quantity'];
        }
        
        $deliveryFee = ($this->order_type === 'delivery') ? 150.00 : 0;
        $total = $subtotal + $deliveryFee;

        // Create Pending Order
        $order = DB::transaction(function () use ($total) {
            $addressData = [];
            if ($this->order_type === 'delivery') {
                $addressData = [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'phone' => $this->phone,
                    'street' => $this->street,
                    'city' => $this->city,
                    'province' => $this->province,
                    'postal' => $this->postal,
                    'instructions' => $this->instructions,
                ];
            } else {
                 $addressData = [
                    'pickup_name' => $this->pickup_name,
                    'pickup_phone' => $this->pickup_phone,
                ];
            }

            // Status: 'pending' (for Cash) or 'unpaid' (for Stripe) - using 'unpaid' for Stripe to be clear
            $initialStatus = ($this->payment_method === 'stripe') ? 'unpaid' : 'pending';

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_type' => $this->order_type,
                'status' => $initialStatus,
                'total' => $total,
                'payment_method' => $this->payment_method, // 'stripe' or 'cash'
                'address_json' => $addressData,
            ]);

            foreach ($this->cart as $drinkId => $details) {
                $order->items()->create([
                     'drink_id' => $drinkId,
                     'drink_name' => $details['name'],
                     'unit_price' => $details['price'],
                     'quantity' => $details['quantity'],
                     'customizations' => $details['customizations'] ?? null,
                ]);
            }
            
            return $order;
        });

        if ($this->payment_method === 'stripe') {
            // Initiate Stripe Checkout
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $lineItems = [];
            foreach ($this->cart as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'lkr', // Or your currency
                        'product_data' => [
                            'name' => $item['name'],
                        ],
                        'unit_amount' => (int) ($item['price'] * 100), // In cents
                    ],
                    'quantity' => $item['quantity'],
                ];
            }
            
            // Add delivery fee if applicable
            if ($deliveryFee > 0) {
                 $lineItems[] = [
                    'price_data' => [
                        'currency' => 'lkr',
                        'product_data' => [
                            'name' => 'Delivery Fee',
                        ],
                        'unit_amount' => (int) ($deliveryFee * 100),
                    ],
                    'quantity' => 1,
                ];
            }

            try {
                $checkoutSession = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('checkout.cancel'),
                    'metadata' => [
                        'order_id' => $order->id,
                    ],
                ]);
                
                return redirect($checkoutSession->url);
                
            } catch (\Exception $e) {
                $order->delete(); // Rollback order if Stripe fails to init
                session()->flash('error', 'Could not initialize payment: ' . $e->getMessage());
                return;
            }

        } else {
            // Cash Flow
            Session::forget('cart');
            Session::flash('success', 'Order placed successfully!');
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        return view('livewire.user.checkout')->layout('layouts.guest');
    }
}

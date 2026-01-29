<?php

namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderDetails extends Component
{
    use AuthorizesRequests;

    public Order $order;

    public function mount(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.user.order-details')->layout('layouts.app');
    }
}

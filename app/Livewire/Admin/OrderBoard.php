<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderBoard extends Component
{
    use WithPagination;

    public $viewingOrderId = null;
    public $search = '';
    public $statusFilter = 'all';
    public $confirmingOrderDeletion = false;
    public $orderIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all', 'as' => 'status'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);
        session()->flash('message', "Order #{$orderId} status updated to {$status}.");
    }

    public function confirmOrderDeletion($id)
    {
        $this->confirmingOrderDeletion = true;
        $this->orderIdBeingDeleted = $id;
    }

    public function deleteOrder()
    {
        $order = Order::findOrFail($this->orderIdBeingDeleted);
        $order->delete(); // Cascade delete handles items if configured, or use model boot events
        
        $this->confirmingOrderDeletion = false;
        $this->orderIdBeingDeleted = null;
        session()->flash('message', 'Order deleted successfully.');
    }

    public function render()
    {
        $query = Order::with(['user', 'items.drink'])
            ->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('id', 'like', '%'.$this->search.'%')
                  ->orWhereHas('user', function($u) {
                      $u->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                  });
            });
        }

        return view('livewire.admin.order-board', [
            'orders' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}

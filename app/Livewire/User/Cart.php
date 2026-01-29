<?php

namespace App\Livewire\User;

use App\Models\Drink;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Cart extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = Session::get('cart', []);
    }

    public function increment($key)
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity']++;
            $this->updateSession();
        }
    }

    public function decrement($key)
    {
        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['quantity'] > 1) {
                $this->cart[$key]['quantity']--;
            }
            $this->updateSession();
        }
    }

    public function remove($key)
    {
        unset($this->cart[$key]);
        $this->updateSession();
    }

    public function clear()
    {
        $this->cart = [];
        $this->updateSession();
    }

    protected function updateSession()
    {
        Session::put('cart', $this->cart);
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            return;
        }

        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.user.cart')->layout('layouts.guest');
    }
}

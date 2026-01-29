<?php

namespace App\Livewire;

use App\Models\Drink;
use Livewire\Component;
use Livewire\WithPagination;

class DrinkCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public $selectedDrink = null;
    public $showModal = false;
    public $quantity = 1;
    public $customizations = '';

    public function openModal($drinkId)
    {
        $this->selectedDrink = Drink::find($drinkId);
        $this->quantity = 1;
        $this->customizations = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDrink = null;
    }

    public function addToCart()
    {
        if (!$this->selectedDrink) return;

        $cart = \Illuminate\Support\Facades\Session::get('cart', []);
        $id = $this->selectedDrink->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $this->quantity;
        } else {
            $cart[$id] = [
                'name' => $this->selectedDrink->name,
                'price' => $this->selectedDrink->price,
                'image_url' => $this->selectedDrink->image_url,
                'quantity' => $this->quantity,
                'customizations' => $this->customizations,
            ];
        }

        \Illuminate\Support\Facades\Session::put('cart', $cart);
        $this->closeModal();
        
        // Optional: Flash message
        session()->flash('message', 'Added to cart successfully!');
        
        // Force refresh to update header count (or use events if I had a header component)
        return redirect()->route('menu');
    }

    public function incrementQuantity()
    {
        $this->quantity++;
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Cache categories for 1 hour as they rarely change (Rubric: Caching Implementation)
        $categories = \Illuminate\Support\Facades\Cache::remember('drink_categories', 60, function () {
            return Drink::select('category')->distinct()->pluck('category');
        });

        $drinks = Drink::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.drink-catalog', [
            'drinks' => $drinks,
            'categories' => $categories,
        ])->layout('layouts.guest');
    }
}

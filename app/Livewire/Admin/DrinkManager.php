<?php

namespace App\Livewire\Admin;

use App\Models\Drink;
use Livewire\Component;
use Livewire\WithPagination;

class DrinkManager extends Component
{
    use WithPagination;

    public $confirmingDrinkDeletion = false;
    public $confirmingDrinkAdd = false;
    public $drinkIdBeingDeleted;

    public $state = [
        'name' => '',
        'price' => '',
        'category' => 'Smoothies',
        'image_url' => '',
        'description' => '',
        'is_featured' => false,
    ];

    public $drinkIdBeingEdited = null;

    protected $rules = [
        'state.name' => 'required|string|min:3',
        'state.price' => 'required|numeric|min:0',
        'state.category' => 'required|string',
        'state.image_url' => 'nullable|url',
        'state.description' => 'nullable|string',
        'state.is_featured' => 'boolean',
    ];

    public function confirmDrinkAdd()
    {
        $this->resetState();
        $this->confirmingDrinkAdd = true;
        $this->drinkIdBeingEdited = null;
    }

    public function confirmDrinkEdit($id)
    {
        $drink = Drink::findOrFail($id);
        $this->drinkIdBeingEdited = $id;
        $this->state = [
            'name' => $drink->name,
            'price' => $drink->price,
            'category' => $drink->category,
            'image_url' => $drink->image_url,
            'description' => $drink->description,
            'is_featured' => (bool) $drink->is_featured,
        ];
        $this->confirmingDrinkAdd = true; // Reusing the same modal
    }

    public function saveDrink()
    {
        $this->validate();

        if ($this->drinkIdBeingEdited) {
            $drink = Drink::findOrFail($this->drinkIdBeingEdited);
            $drink->update($this->state);
        } else {
            Drink::create($this->state);
        }

        $this->drinkIdBeingEdited = null;
        $this->resetState();
        session()->flash('message', 'Drink saved successfully.');
    }

    public function confirmDrinkDeletion($id)
    {
        $this->confirmingDrinkDeletion = true;
        $this->drinkIdBeingDeleted = $id;
    }

    public function deleteDrink()
    {
        $drink = Drink::findOrFail($this->drinkIdBeingDeleted);
        $drink->delete();

        $this->confirmingDrinkDeletion = false;
        $this->drinkIdBeingDeleted = null;
        session()->flash('message', 'Drink deleted successfully.');
    }

    public function cancelEdit()
    {
        $this->resetState();
        $this->drinkIdBeingEdited = null;
    }

    public function resetState()
    {
        $this->state = [
            'name' => '',
            'price' => '',
            'category' => 'Smoothies',
            'image_url' => '',
            'description' => '',
            'is_featured' => false,
        ];
    }

    public function render()
    {
        $drinks = Drink::latest()->paginate(10);
        return view('livewire.admin.drink-manager', [
            'drinks' => $drinks,
        ])->layout('layouts.app');
    }
}

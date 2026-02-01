<?php

namespace Tests\Feature;

use App\Livewire\DrinkCatalog;
use App\Models\Drink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DrinkCatalogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * #CAT-01: View Catalog
     * User visits the drinks menu page.
     */
    public function test_catalog_page_loads_with_drinks(): void
    {
        $drink = Drink::factory()->create(['name' => 'Mojito']);
        
        Livewire::test(DrinkCatalog::class)
            ->assertStatus(200)
            ->assertSee('Mojito');
    }

    /**
     * #CAT-02: Search Drink
     * User searches for a specific drink name.
     */
    public function test_catalog_search_functionality(): void
    {
        Drink::factory()->create(['name' => 'Mojito']);
        Drink::factory()->create(['name' => 'Cosmopolitan']);

        Livewire::test(DrinkCatalog::class)
            ->set('search', 'Moji')
            ->assertSee('Mojito')
            ->assertDontSee('Cosmopolitan');
    }

    /**
     * #CAT-03: Filter Category
     * User selects a specific category.
     */
    public function test_catalog_category_filter(): void
    {
        Drink::factory()->create(['name' => 'Beer A', 'category' => 'Beer']);
        Drink::factory()->create(['name' => 'Wine B', 'category' => 'Wine']);

        Livewire::test(DrinkCatalog::class)
            ->set('category', 'Beer')
            ->assertSee('Beer A')
            ->assertDontSee('Wine B');
    }

    /**
     * #CAT-04: Add to Cart
     * User clicks "Add to Cart" on a drink.
     */
    public function test_catalog_add_to_cart(): void
    {
        $drink = Drink::factory()->create();

        Livewire::test(DrinkCatalog::class)
            ->call('openModal', $drink->id)
            ->set('quantity', 2)
            ->call('addToCart')
            ->assertRedirect(route('menu'));

        $cart = session('cart');
        $this->assertArrayHasKey($drink->id, $cart);
        $this->assertEquals(2, $cart[$drink->id]['quantity']);
    }
}

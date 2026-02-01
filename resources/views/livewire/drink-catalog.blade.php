<div class="font-open-sans">
    
    <!-- Hero -->
    <section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-16 text-center">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl md:text-5xl font-poppins font-bold mb-2">Fresh Menu</h2>
        <p class="text-xl text-gray-100 max-w-2xl mx-auto">Discover our carefully crafted collection of fresh juices, smoothies, and seasonal specials.</p>
      </div>
    </section>

    <!-- Filters: search + category chips -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6 items-center justify-between w-full">
          <!-- Search box -->
          <div class="w-full lg:w-1/2 relative">
            <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-deep-teal" placeholder="Search for your favorite drink..." />
          </div>
    
          <!-- Category chips -->
          <div class="flex flex-wrap gap-3">
            @php
                $cats = ['' => 'All', 'Juices' => 'Fresh Juices', 'Smoothies' => 'Smoothies', 'Seasonal' => 'Seasonal'];
            @endphp
            @foreach ($cats as $k => $label)
                <button wire:click="$set('category', '{{ $k }}')" 
                    class="px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300 {{ $category === $k ? 'bg-deep-teal text-white' : 'bg-gray-100 text-charcoal hover:bg-deep-teal hover:text-white' }}">
                    {{ $label }}
                </button>
            @endforeach
          </div>
        </div>
      </div>
    </section>

    <!-- Cards grid -->
    <section class="py-12 bg-soft-cream">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($drinks as $drink)
                @php
                     // Legacy badge logic adaptation (reused)
                    $name = strtolower($drink->name);
                    $cat = strtolower($drink->category);
                    $badge = ''; $badgeClass = '';
                    
                    if ($drink->is_featured) { $badge='Popular'; $badgeClass='bg-olive-green'; }
                    elseif (str_contains($name,'shot')) { $badge='Shot'; $badgeClass='bg-deep-teal'; }
                    elseif (str_contains($name,'refresh')) { $badge='Refreshing'; $badgeClass='bg-olive-green'; }
                    elseif ($cat === 'seasonal') { $badge='New'; $badgeClass='bg-muted-coral'; }
                    elseif (str_contains($name,'detox') || str_contains($name,'green')) { $badge='Detox'; $badgeClass='bg-olive-green'; }
                    elseif (str_contains($name,'orange')) { $badge='Classic'; $badgeClass='bg-deep-teal'; }
                    
                    // Pseudo rating
                    $seed = crc32(strtolower($drink->name));
                    $rating = number_format(4.3 + (($seed % 70) / 100), 1);
                    $full = floor($rating);
                    $half = ($rating - $full) >= 0.5 ? 1 : 0;
                    $empty = 5 - $full - $half;

                    // Short Desc
                    $desc = 'Naturally sweet, blended fresh to order. Great taste, great energy.';
                    if (str_contains($name,'green')) $desc = 'Spinach, banana, apple, ginger & coconut water. Packed with vitamins & minerals.';
                    elseif (str_contains($name,'tropical')) $desc = 'Mango, pineapple, passion fruit & coconut milk. A tropical escape in every sip.';
                    elseif (str_contains($name,'orange')) $desc = '100% pure fresh orange juice. No additives—just vitamin C goodness.';
                    elseif (str_contains($name,'carrot')) $desc = 'Fresh carrot and ginger. Perfect immunity booster.';
                    elseif (str_contains($name,'berry')) $desc = 'Mixed berries blended smooth. Antioxidant rich and delicious.';
                    elseif (str_contains($name,'citrus')) $desc = 'Orange, lemon & lime. Bright, zesty, super refreshing.';
                @endphp
                
                <div class="bg-white rounded-3xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300">
                    <!-- Image -->
                    <div class="relative overflow-hidden rounded-2xl mb-6 aspect-[4/3] sm:aspect-[16/9]">
                        <img src="{{ $drink->image_url }}" alt="{{ $drink->name }}" class="absolute inset-0 w-full h-full object-cover">
                        @if($badge)
                            <div class="absolute top-4 right-4 {{ $badgeClass }} text-white px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $badge }}
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="space-y-3">
                        <h4 class="text-xl font-poppins font-bold text-charcoal">{{ $drink->name }}</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $desc }}</p>

                        <!-- Rating -->
                        <div class="flex items-center gap-2">
                            <div class="text-yellow-400">
                                @for($i=0;$i<$full;$i++) <i class="fas fa-star"></i> @endfor
                                @if($half) <i class="fas fa-star-half-alt"></i> @endif
                                @for($i=0;$i<$empty;$i++) <i class="far fa-star"></i> @endfor
                            </div>
                            <span class="text-xs text-gray-500">({{ $rating }})</span>
                        </div>

                        <!-- Price + CTA -->
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-2xl font-bold text-deep-teal">Rs. {{ number_format($drink->price, 2) }}</span>
                            <button wire:click="openModal({{ $drink->id }})" class="bg-muted-coral text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-all duration-300">
                                Customize
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-500">
                    No drinks found matching your criteria.
                </div>
            @endforelse
          </div>

          <div class="mt-12">
            {{ $drinks->links() }}
          </div>
      </div>
    </section>

    <!-- Modal -->
    @if($showModal && $selectedDrink)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="background-color: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-3xl shadow-xl max-w-lg w-full overflow-hidden transform transition-all">
            <div class="relative">
                <img src="{{ $selectedDrink->image_url }}" alt="{{ $selectedDrink->name }}" class="w-full h-48 object-cover">
                <button wire:click="closeModal" class="absolute top-4 right-4 bg-white/80 p-2 rounded-full hover:bg-white text-gray-800 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <h3 class="text-2xl font-poppins font-bold text-charcoal">{{ $selectedDrink->name }}</h3>
                <p class="text-gray-600 mt-2 text-sm">{{ $selectedDrink->description ?? 'Freshly blended for you.' }}</p>
                <div class="text-deep-teal font-bold text-xl mt-3">Rs. {{ number_format($selectedDrink->price, 2) }}</div>

                <div class="mt-6 space-y-4">
                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <div class="flex items-center gap-4">
                            <button wire:click="decrementQuantity" class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 text-xl font-bold text-gray-600">-</button>
                            <span class="text-xl font-semibold w-8 text-center">{{ $quantity }}</span>
                            <button wire:click="incrementQuantity" class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 text-xl font-bold text-gray-600">+</button>
                        </div>
                    </div>

                    <!-- Customizations -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions (Optional)</label>
                         <textarea wire:model="customizations" class="w-full border-gray-300 rounded-xl focus:border-deep-teal focus:ring focus:ring-deep-teal/20" rows="3" placeholder="Less ice, extra ginger, etc."></textarea>
                    </div>
                </div>

                <div class="mt-8">
                    <button wire:click="addToCart" class="w-full bg-deep-teal text-white py-3 rounded-xl font-semibold hover:bg-opacity-90 transition shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-shopping-bag"></i>
                        Add to Order - Rs. {{ number_format($selectedDrink->price * $quantity, 2) }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

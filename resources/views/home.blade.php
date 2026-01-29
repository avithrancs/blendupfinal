<x-guest-layout>
    <!-- HERO section -->
    <section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <div class="space-y-6">
            <h2 class="text-4xl md:text-6xl font-poppins font-bold leading-tight">
              Fresh Juices & Smoothies <span class="text-muted-coral">Delivered</span>
            </h2>
            <p class="text-xl text-gray-100 font-light leading-relaxed">
              Experience the perfect blend of taste and nutrition with our premium selection of fresh juices and smoothies, crafted with love and delivered to your doorstep.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
              <a href="{{ route('menu') }}" class="bg-muted-coral text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-opacity-90 transition-all duration-300 text-center transform hover:scale-105">
                Order Now
              </a>
              <a href="{{ route('menu') }}" class="border-2 border-white text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white hover:text-deep-teal transition-all duration-300 text-center">
                View Menu
              </a>
            </div>
          </div>
    
          <!-- Hero image card -->
          <div class="relative">
            <div class="bg-white rounded-3xl p-8 shadow-2xl rotate-1">
              <img src="https://images.unsplash.com/photo-1546173159-315724a31696?w=900&auto=format&fit=crop" class="w-full h-80 object-cover rounded-2xl" alt="">
              <div class="absolute -top-4 -right-4 bg-muted-coral text-white rounded-full w-16 h-16 flex items-center justify-center font-bold text-lg shadow">
                Fresh!
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!--  TODAY'S SPECIALS -->
    <section id="featured" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="text-center mb-16">
            <h3 class="text-4xl font-poppins font-bold text-charcoal mb-4">Today's Specials</h3>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Handpicked fresh ingredients blended to perfection. Start your healthy journey with our signature drinks.</p>
        </div>
    
        <!-- Specials grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($specials as $d)
                @php
                    // Legacy badge logic adaptation
                    $name = strtolower($d->name);
                    $cat = strtolower($d->category);
                    $badge = ''; $badgeClass = '';
                    
                    if ($d->is_featured) { $badge='Popular'; $badgeClass='bg-olive-green'; }
                    elseif (str_contains($name,'shot')) { $badge='Shot'; $badgeClass='bg-deep-teal'; }
                    elseif (str_contains($name,'refresh')) { $badge='Refreshing'; $badgeClass='bg-olive-green'; }
                    elseif ($cat === 'seasonal') { $badge='New'; $badgeClass='bg-muted-coral'; }
                    elseif (str_contains($name,'detox') || str_contains($name,'green')) { $badge='Detox'; $badgeClass='bg-olive-green'; }
                    elseif (str_contains($name,'orange')) { $badge='Classic'; $badgeClass='bg-deep-teal'; }
                    else { $badge='Bestseller'; $badgeClass='bg-deep-teal'; }
    
                    // Pseudo rating
                    $seed = crc32(strtolower($d->name));
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
    
                <div class="bg-soft-cream rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
                    <!-- Card image + badge -->
                    <div class="relative overflow-hidden rounded-2xl mb-6">
                        <img src="{{ $d->image_url }}" alt="{{ $d->name }}" class="w-full h-48 object-cover">
                        <div class="absolute top-4 right-4 {{ $badgeClass }} text-white px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $badge }}
                        </div>
                    </div>
    
                    <!-- Card content -->
                    <h4 class="text-xl font-poppins font-bold text-charcoal">{{ $d->name }}</h4>
                    <p class="text-sm text-gray-600 leading-relaxed mt-1">{{ $desc }}</p>
    
                    <!-- Rating -->
                    <div class="flex items-center gap-2 mt-2">
                        <div class="text-yellow-400">
                            @for($i=0;$i<$full;$i++) <i class="fas fa-star"></i> @endfor
                            @if($half) <i class="fas fa-star-half-alt"></i> @endif
                            @for($i=0;$i<$empty;$i++) <i class="far fa-star"></i> @endfor
                        </div>
                        <span class="text-xs text-gray-500">({{ $rating }})</span>
                    </div>
    
                    <!-- Price + CTA -->
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-2xl font-bold text-deep-teal">${{ number_format($d->price, 2) }}</span>
    
                        <a href="{{ route('menu', ['q' => $d->name]) }}"
                           class="bg-muted-coral text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-all duration-300">
                            Customize
                        </a>
                    </div>
                </div>
            @empty
                <!-- Fallback cards -->
                @for($i=0;$i<3;$i++)
                    <div class="bg-soft-cream rounded-3xl p-6 shadow-lg opacity-80">
                        <div class="relative overflow-hidden rounded-2xl mb-6">
                            <div class="w-full h-48 bg-gray-200"></div>
                            <div class="absolute top-4 right-4 bg-gray-500 text-white px-3 py-1 rounded-full text-sm font-semibold">Coming Soon</div>
                        </div>
                        <h4 class="text-xl font-poppins font-bold text-charcoal mb-1">New Drink</h4>
                        <p class="text-sm text-gray-600">Blended fresh to order. Great taste, great energy.</p>
                    </div>
                @endfor
            @endforelse
        </div>
      </div>
    </section>
    
    <!-- "WHY CHOOSE BLENDUP" (features) -->
    <section class="py-20 bg-soft-cream">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-3xl md:text-4xl font-poppins font-bold text-center text-charcoal">Why Choose BlendUp?</h3>
        <div class="mt-12 grid md:grid-cols-3 gap-8">
          <div class="text-center bg-soft-cream rounded-3xl ">
            <div class="mx-auto w-14 h-14 rounded-full bg-olive-green/15 flex items-center justify-center mb-4">
              <i class="fas fa-leaf text-olive-green"></i>
            </div>
            <h4 class="font-poppins font-bold text-lg mb-1">100% Natural</h4>
            <p class="text-gray-600 text-sm">No artificial flavors, colors, or preservatives. Just pure, natural goodness.</p>
          </div>
          <div class="text-center bg-soft-cream rounded-3xl ">
            <div class="mx-auto w-14 h-14 rounded-full bg-muted-coral/15 flex items-center justify-center mb-4">
              <i class="fas fa-shipping-fast text-muted-coral"></i>
            </div>
            <h4 class="font-poppins font-bold text-lg mb-1">Fast Delivery</h4>
            <p class="text-gray-600 text-sm">Fresh smoothies delivered to your doorstep within 30 minutes.</p>
          </div>
          <div class="text-center bg-soft-cream rounded-3xl">
            <div class="mx-auto w-14 h-14 rounded-full bg-deep-teal/15 flex items-center justify-center mb-4">
              <i class="fas fa-heart text-deep-teal"></i>
            </div>
            <h4 class="font-poppins font-bold text-lg mb-1">Made with Love</h4>
            <p class="text-gray-600 text-sm">Each drink is crafted carefully by our passionate team.</p>
          </div>
        </div>
      </div>
    </section>
</x-guest-layout>

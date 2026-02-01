<div class="font-open-sans">
    <!-- HERO -->
    <section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-poppins font-bold">Shopping Cart</h1>
        <p class="text-white/90 mt-2">Review your fresh selections and customize your perfect order.</p>
      </div>
    </section>

    <!-- BODY -->
    <section class="py-10 bg-soft-cream min-h-[60vh]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-8">
    
        <!-- LEFT: Items card -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-3xl shadow p-6">
            <h3 class="font-poppins font-bold text-lg mb-4">Your Cart Items</h3>
    
            @if (empty($cart))
              <div class="text-center text-gray-600 py-10">
                Your cart is empty.
                <a href="{{ route('menu') }}" class="text-deep-teal underline">Browse the menu</a>
              </div>
            @else
              <div class="space-y-4">
                @foreach ($cart as $key => $item)
                    <div class="flex items-center justify-between gap-4 rounded-2xl px-4 py-3">
                        <!-- left group: thumb + meta -->
                        <div class="flex items-center gap-4">
                            <img src="{{ $item['image_url'] ?? asset('assets/img/placeholder.jpg') }}" class="w-14 h-14 object-cover rounded-xl" alt="">
                            <div>
                                <div class="font-semibold text-charcoal leading-tight">{{ $item['name'] }}</div>
                                <!-- chips -->
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <!-- Customizations logic to come here -->
                                </div>
                                <div class="text-sm text-muted-coral mt-1 font-semibold">Rs. {{ number_format($item['price'], 2) }}</div>
                            </div>
                        </div>

                        <!-- right: qty stepper + trash -->
                        <div class="flex items-center gap-2">
                             <button wire:click="decrement('{{ $key }}')" class="w-9 h-9 rounded-full border flex items-center justify-center hover:bg-gray-50">−</button>
                             <div class="w-10 text-center text-sm">{{ $item['quantity'] }}</div>
                             <button wire:click="increment('{{ $key }}')" class="w-9 h-9 rounded-full border flex items-center justify-center hover:bg-gray-50">+</button>
                             
                             <button wire:click="remove('{{ $key }}')" class="w-9 h-9 rounded-full border border-red-500 text-red-600 flex items-center justify-center hover:bg-red-500 hover:text-white transition">
                                <i class="fas fa-trash"></i>
                             </button>
                        </div>
                    </div>
                @endforeach
              </div>
    
              <div class="mt-5">
                 <button wire:click="clear" onclick="return confirm('Clear the entire cart?')" class="px-4 py-2 rounded-xl border text-gray-700 hover:bg-gray-100">Clear Cart</button>
              </div>
            @endif
          </div>
        </div>
    
        <!-- RIGHT: Summary card -->
        @php
            $subtotal = 0;
            foreach($cart as $c) { $subtotal += $c['price'] * $c['quantity']; }
            $delivery = count($cart) > 0 ? 150.00 : 0;
            $tax = $subtotal * 0.08;
            $total = $subtotal + $delivery + $tax;
        @endphp
        <aside class="bg-white rounded-3xl shadow p-6 h-fit">
          <h3 class="font-poppins font-bold text-lg mb-4">Order Summary</h3>
    
          <div class="space-y-2 text-sm text-gray-700">
            <div class="flex justify-between">
              <span>Subtotal</span>
              <span>Rs. {{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Delivery Fee</span>
              <span>Rs. {{ number_format($delivery, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Tax</span>
              <span>Rs. {{ number_format($tax, 2) }}</span>
            </div>
            <div class="flex justify-between font-semibold text-base pt-2 border-t">
              <span>Total</span>
              <span class="text-deep-teal">Rs. {{ number_format($total, 2) }}</span>
            </div>
          </div>
    
          <!-- Promo (visual) -->
          <div class="flex gap-2 mt-4">
            <input class="flex-1 px-3 py-2 border rounded-xl" placeholder="Promo code">
            <button class="px-4 py-2 rounded-xl border">Apply</button>
          </div>
    
          <button wire:click="checkout" wire:loading.attr="disabled" class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-deep-teal text-white px-4 py-3 rounded-2xl hover:bg-opacity-90 transition disabled:opacity-50">
            <i wire:loading.remove class="fas fa-shopping-bag"></i>
            <i wire:loading class="fas fa-spinner fa-spin"></i>
            <span wire:loading.remove>Proceed to Checkout</span>
            <span wire:loading>Processing...</span>
          </button>
    
          <a href="{{ route('menu') }}" class="mt-3 w-full inline-flex items-center justify-center gap-2 bg-muted-coral text-white px-4 py-3 rounded-2xl hover:bg-opacity-90 transition">
            <i class="fas fa-arrow-left"></i>
            Continue Shopping
          </a>
    
          <div class="mt-4 space-y-2 text-sm text-charcoal">
            <div class="flex items-start gap-2">
              <i class="fas fa-thumbs-up mt-0.5 text-deep-teal"></i>
              <span>Free delivery on orders over Rs. 5000</span>
            </div>
            <div class="flex items-start gap-2">
              <i class="fas fa-clock mt-0.5 text-muted-coral"></i>
              <span>Estimated delivery: 25–35 minutes</span>
            </div>
          </div>
        </aside>
      </div>
    </section>
</div>

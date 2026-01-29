<div class="font-open-sans">
    <section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-poppins font-bold mb-4">Checkout & Payment</h2>
        <p class="text-xl text-white/90">Complete your order and choose your preferred delivery method and payment option.</p>
      </div>
    </section>

    <section class="py-12 bg-soft-cream min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-8">

        <!-- Checkout form -->
        <div class="lg:col-span-2 space-y-8">
          
          <!-- Order Type -->
          <div class="bg-white rounded-3xl p-8 shadow-lg">
            <h3 class="text-2xl font-poppins font-bold text-charcoal mb-6 flex items-center">
              <i class="fas fa-truck text-deep-teal mr-3"></i> Order Type
            </h3>
            
            <div class="flex gap-4">
              <label class="cursor-pointer">
                  <input type="radio" wire:model.live="order_type" value="delivery" class="hidden peer">
                  <div class="px-6 py-2 rounded-full border-2 border-gray-200 peer-checked:border-deep-teal peer-checked:bg-deep-teal/10 peer-checked:text-deep-teal font-semibold transition">
                      Delivery
                  </div>
              </label>
              <label class="cursor-pointer">
                  <input type="radio" wire:model.live="order_type" value="pickup" class="hidden peer">
                  <div class="px-6 py-2 rounded-full border-2 border-gray-200 peer-checked:border-deep-teal peer-checked:bg-deep-teal/10 peer-checked:text-deep-teal font-semibold transition">
                      Pickup
                  </div>
              </label>
            </div>

            <!-- Delivery Fields -->
            @if($order_type === 'delivery')
            <div class="mt-6 space-y-4 animate-fadeIn">
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <input wire:model="first_name" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="First Name *">
                    @error('first_name') <span class="text-red-500 text-sm ml-2">{{ $message }}</span> @enderror
                </div>
                <div>
                    <input wire:model="last_name" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="Last Name *">
                    @error('last_name') <span class="text-red-500 text-sm ml-2">{{ $message }}</span> @enderror
                </div>
              </div>
              <div>
                  <input wire:model="phone" type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="Contact Number *">
                  @error('phone') <span class="text-red-500 text-sm ml-2">{{ $message }}</span> @enderror
              </div>
              <div>
                  <input wire:model="street" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="Street Address *">
                  @error('street') <span class="text-red-500 text-sm ml-2">{{ $message }}</span> @enderror
              </div>
              <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <input wire:model="city" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="City *">
                    @error('city') <span class="text-red-500 text-sm ml-2">{{ $message }}</span> @enderror
                </div>
                <input wire:model="province" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="Province">
                <input wire:model="postal" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="Postal Code">
              </div>
              <textarea wire:model="instructions" class="w-full px-4 py-3 border border-gray-300 rounded-2xl h-24 focus:ring-deep-teal focus:border-deep-teal" placeholder="Delivery Instructions (Optional)"></textarea>
            </div>
            @endif

            <!-- Pickup Fields -->
            @if($order_type === 'pickup')
            <div class="mt-6 space-y-4 animate-fadeIn">
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <input wire:model="pickup_name" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="Your Name *">
                    @error('pickup_name') <span class="text-red-500 text-sm ml-2">{{ $message }}</span> @enderror
                </div>
                <div>
                    <input wire:model="pickup_phone" type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:ring-deep-teal focus:border-deep-teal" placeholder="Contact Number *">
                    @error('pickup_phone') <span class="text-red-500 text-sm ml-2">{{ $message }}</span> @enderror
                </div>
              </div>
              <div class="bg-muted-coral/10 rounded-2xl p-4 text-sm text-charcoal flex items-start">
                <i class="fas fa-clock text-muted-coral mr-2 mt-0.5"></i>
                <span>Your order will be ready for pickup in 15–20 minutes after confirmation.</span>
              </div>
            </div>
            @endif
          </div>

          <!-- Payment -->
          <div class="bg-white rounded-3xl p-8 shadow-lg">
            <h3 class="text-2xl font-poppins font-bold text-charcoal mb-6 flex items-center">
              <i class="fas fa-credit-card text-deep-teal mr-3"></i> Payment Method
            </h3>
            
            <div class="flex gap-4">
              <label class="cursor-pointer">
                  <input type="radio" wire:model.live="payment_method" value="stripe" class="hidden peer">
                  <div class="px-6 py-2 rounded-full border-2 border-gray-200 peer-checked:border-deep-teal peer-checked:bg-deep-teal/10 peer-checked:text-deep-teal font-semibold transition">
                      Online Payment
                  </div>
              </label>
              <label class="cursor-pointer">
                  <input type="radio" wire:model.live="payment_method" value="cash" class="hidden peer">
                  <div class="px-6 py-2 rounded-full border-2 border-gray-200 peer-checked:border-deep-teal peer-checked:bg-deep-teal/10 peer-checked:text-deep-teal font-semibold transition">
                      Cash on Delivery
                  </div>
              </label>
            </div>

            @if($payment_method === 'stripe')
            <div class="mt-6 text-sm text-charcoal animate-fadeIn">
              <div class="bg-deep-teal/10 rounded-2xl p-4 flex items-start">
                <i class="fas fa-lock text-deep-teal mr-2 mt-0.5"></i>
                <p>You will be redirected to Stripe to securely complete your payment.</p>
              </div>
            </div>
            @else
            <div class="mt-6 text-sm text-charcoal animate-fadeIn">
              <div class="bg-olive-green/10 rounded-2xl p-4 flex items-start">
                <i class="fas fa-money-bill-wave text-olive-green mr-2 mt-0.5"></i>
                <p>Pay with cash when your order arrives. Please have the exact amount ready.</p>
              </div>
            </div>
            @endif
          </div>

          <button wire:click="placeOrder" wire:loading.attr="disabled" class="w-full bg-deep-teal text-white text-xl font-bold py-4 rounded-full hover:bg-opacity-90 transition shadow-lg disabled:opacity-50">
            <span wire:loading.remove>
                {{ $payment_method === 'stripe' ? 'Pay Now' : 'Place Order' }}
            </span>
            <span wire:loading>Processing Order...</span>
          </button>
        </div>

        <!-- Order Summary -->
        <aside class="bg-white rounded-3xl p-8 shadow-lg h-fit">
          <h3 class="text-2xl font-poppins font-bold text-charcoal mb-6 flex items-center">
            <i class="fas fa-receipt text-deep-teal mr-3"></i> Order Summary
          </h3>
          
          <div class="space-y-3 text-sm text-gray-700">
            @php $subtotal = 0; @endphp
            @foreach($cart as $item)
                @php $subtotal += $item['price'] * $item['quantity']; @endphp
                <div class="flex items-center justify-between">
                    <span>{{ $item['name'] }} <span class="text-gray-400">× {{ $item['quantity'] }}</span></span>
                    <span class="font-semibold">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                </div>
            @endforeach
            
            <div class="border-t border-gray-100 pt-4 mt-4 space-y-2">
              <div class="flex justify-between">
                  <span>Subtotal</span>
                  <span>${{ number_format($subtotal, 2) }}</span>
              </div>
              
              <div class="flex justify-between {{ $order_type === 'delivery' ? 'text-charcoal' : 'text-gray-400 line-through' }}">
                  <span>Delivery</span>
                  <span>${{ $order_type === 'delivery' ? '2.50' : '0.00' }}</span>
              </div>
              
              <div class="flex justify-between font-bold text-lg text-deep-teal pt-2 border-t border-gray-100 mt-2">
                  <span>Total</span>
                  <span>${{ number_format($subtotal + ($order_type === 'delivery' ? 2.50 : 0), 2) }}</span>
              </div>
            </div>
          </div>
        </aside>

      </div>
    </section>
</div>

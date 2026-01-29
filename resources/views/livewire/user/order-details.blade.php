<div class="py-12 bg-soft-cream min-h-screen font-open-sans">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-deep-teal hover:underline mb-6 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>

        <div class="grid md:grid-cols-3 gap-8">
            
            <!-- Details Column -->
            <div class="md:col-span-2 space-y-6">
                
                <!-- Status Card -->
                <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-3xl font-poppins font-bold text-charcoal">Order #{{ $order->id }}</h1>
                            <p class="text-gray-500 mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                        <span class="px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                'bg-yellow-100 text-yellow-800') }}">
                            {{ $order->status }}
                        </span>
                    </div>

                    <!-- Progress Bar (Visual) -->
                    <div class="relative mt-8 mb-4">
                        <div class="absolute top-1/2 left-0 w-full h-2 bg-gray-100 rounded-full transform -translate-y-1/2"></div>
                        <!-- Active Progress (Mocked based on status) -->
                        @php
                            $progress = 10;
                            if($order->status == 'processing') $progress = 50;
                            if($order->status == 'delivered' || $order->status == 'ready_for_pickup') $progress = 100;
                        @endphp
                        <div class="absolute top-1/2 left-0 h-2 bg-deep-teal rounded-full transform -translate-y-1/2 transition-all duration-1000" style="width: {{ $progress }}%"></div>
                        
                        <div class="relative flex justify-between uppercase text-xs font-bold text-gray-400 tracking-wider">
                            <div class="flex flex-col items-center gap-2 {{ $progress >= 10 ? 'text-deep-teal' : '' }}">
                                <div class="w-4 h-4 rounded-full bg-current"></div>
                                <span>Placed</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 {{ $progress >= 50 ? 'text-deep-teal' : '' }}">
                                <div class="w-4 h-4 rounded-full bg-current"></div>
                                <span>Processing</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 {{ $progress >= 100 ? 'text-deep-teal' : '' }}">
                                <div class="w-4 h-4 rounded-full bg-current"></div>
                                <span>{{ $order->order_type == 'pickup' ? 'Ready' : 'Delivered' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Card -->
                <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
                    <h3 class="text-xl font-poppins font-bold text-charcoal mb-6">Items Ordered</h3>
                    <div class="space-y-6">
                        @foreach($order->items as $item)
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center text-gray-300">
                                    <i class="fas fa-coffee text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-charcoal text-lg">{{ $item->drink_name }}</h4>
                                    @if($item->customizations)
                                        <p class="text-sm text-gray-500 italic">{{ $item->customizations }}</p>
                                    @endif
                                    <p class="text-sm text-gray-400 mt-1">Qty: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <p class="font-bold text-charcoal">${{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Summary Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-poppins font-bold text-charcoal mb-4">Payment & Totals</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>${{ number_format($order->total / 1.08, 2) }}</span> 
                        </div>
                        <div class="flex justify-between">
                            <span>Tax & Fees</span>
                            <span>${{ number_format($order->total - ($order->total / 1.08), 2) }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-100 font-bold text-lg text-deep-teal">
                            <span>Total</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-bold mb-1">Payment Method</p>
                        <p class="font-semibold text-charcoal capitalize"><i class="fas fa-credit-card mr-2 text-muted-coral"></i> {{ $order->payment_method }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-poppins font-bold text-charcoal mb-4">{{ $order->order_type == 'delivery' ? 'Delivery Details' : 'Pickup Details' }}</h3>
                    @if($order->order_type == 'delivery' && is_array($order->address_json))
                        <address class="not-italic text-sm text-gray-600 space-y-1">
                            <p class="font-bold text-gray-800">{{ $order->address_json['first_name'] ?? '' }} {{ $order->address_json['last_name'] ?? '' }}</p>
                            <p>{{ $order->address_json['street'] ?? '' }}</p>
                            <p>{{ $order->address_json['city'] ?? '' }}, {{ $order->address_json['province'] ?? '' }} {{ $order->address_json['postal'] ?? '' }}</p>
                            <p class="mt-2 text-deep-teal"><i class="fas fa-phone mr-1"></i> {{ $order->address_json['phone'] ?? '' }}</p>
                        </address>
                    @elseif($order->order_type == 'pickup' && is_array($order->address_json))
                        <div class="text-sm text-gray-600 space-y-1">
                            <p class="font-bold text-gray-800">{{ $order->address_json['pickup_name'] ?? '' }}</p>
                            <p class="mt-2 text-deep-teal"><i class="fas fa-phone mr-1"></i> {{ $order->address_json['pickup_phone'] ?? '' }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

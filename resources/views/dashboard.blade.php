<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome, ') . auth()->user()->name }}
        </h2>
    </x-slot>

    <div class="py-12 bg-soft-cream min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Order Now -->
                <a href="{{ route('menu') }}" class="block p-6 bg-white rounded-2xl shadow-sm hover:shadow-xl transition border-l-4 border-deep-teal group">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-poppins font-bold text-charcoal group-hover:text-deep-teal transition">Order Fresh</h3>
                            <p class="text-sm text-gray-500 mt-1">Browse our menu and order.</p>
                        </div>
                        <div class="bg-deep-teal/10 p-3 rounded-full text-deep-teal group-hover:bg-deep-teal group-hover:text-white transition">
                            <i class="fas fa-blender text-xl"></i>
                        </div>
                    </div>
                </a>

                <!-- Cart -->
                <a href="{{ route('cart') }}" class="block p-6 bg-white rounded-2xl shadow-sm hover:shadow-xl transition border-l-4 border-muted-coral group">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-poppins font-bold text-charcoal group-hover:text-muted-coral transition">My Cart</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ count(session('cart', [])) }} items waiting.</p>
                        </div>
                        <div class="bg-muted-coral/10 p-3 rounded-full text-muted-coral group-hover:bg-muted-coral group-hover:text-white transition">
                            <i class="fas fa-shopping-cart text-xl"></i>
                        </div>
                    </div>
                </a>

                <!-- Profile -->
                <a href="{{ route('profile.show') }}" class="block p-6 bg-white rounded-2xl shadow-sm hover:shadow-xl transition border-l-4 border-olive-green group">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-poppins font-bold text-charcoal group-hover:text-olive-green transition">Settings</h3>
                            <p class="text-sm text-gray-500 mt-1">Update profile & security.</p>
                        </div>
                        <div class="bg-olive-green/10 p-3 rounded-full text-olive-green group-hover:bg-olive-green group-hover:text-white transition">
                            <i class="fas fa-user-cog text-xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Orders Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
                    <h3 class="text-lg font-poppins font-bold text-charcoal">Recent Orders</h3>
                    <button class="text-sm text-deep-teal font-semibold hover:underline">View All</button>
                </div>
                
                <div class="p-6">
                   @if(auth()->user()->orders && auth()->user()->orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-soft-cream text-charcoal uppercase font-bold text-xs tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4 rounded-l-xl">Order #</th>
                                        <th class="px-6 py-4">Date</th>
                                        <th class="px-6 py-4">Items</th>
                                        <th class="px-6 py-4">Total</th>
                                        <th class="px-6 py-4 rounded-r-xl">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach(auth()->user()->orders->take(5) as $order)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 font-mono font-medium text-deep-teal">
                                            <a href="{{ route('orders.show', $order) }}" class="hover:underline">#{{ $order->id }}</a>
                                        </td>
                                        <td class="px-6 py-4">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4">{{ $order->items->count() }} items</td>
                                        <td class="px-6 py-4 font-bold text-charcoal">${{ number_format($order->total, 2) }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                                {{ $order->status === 'delivered' ? 'bg-olive-green/20 text-olive-green' : 
                                                  ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                   @else
                        <div class="text-center py-12">
                            <div class="inline-block p-4 rounded-full bg-soft-cream text-deep-teal mb-4">
                                <i class="fas fa-receipt text-3xl"></i>
                            </div>
                            <h4 class="text-charcoal font-bold font-poppins text-lg">No orders yet</h4>
                            <p class="text-gray-500 mt-1 mb-6">You haven't placed any orders yet. Hungry?</p>
                            <a href="{{ route('menu') }}" class="bg-deep-teal text-white px-8 py-3 rounded-full hover:bg-opacity-90 transition shadow-lg font-semibold inline-flex items-center gap-2">
                                <i class="fas fa-blender"></i> Browse Menu
                            </a>
                        </div>
                   @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

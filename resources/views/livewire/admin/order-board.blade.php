<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8 font-poppins">
    <div class="flex items-center justify-between">
        <h1 class="font-poppins text-2xl font-bold text-gray-800">Manage Orders</h1>
        <a href="{{ route('admin.drinks') }}" class="text-deep-teal underline">← Back to Drinks</a>
    </div>

    @if (session()->has('message'))
        <div class="p-3 rounded-2xl bg-olive-green/15 text-olive-green text-sm">{{ session('message') }}</div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-3xl p-6 shadow flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <div class="flex items-center gap-3">
            <label class="text-sm font-semibold text-gray-700">Status:</label>
            <div class="flex flex-wrap gap-2">
                @foreach (['all' => 'All', 'pending' => 'Pending', 'preparing' => 'Preparing', 'out_for_delivery' => 'Out for Delivery', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $status => $label)
                    <button wire:click="filterByStatus('{{ $status }}')"
                       class="px-4 py-2 rounded-full text-sm font-semibold transition {{ $statusFilter === $status ? 'bg-deep-teal text-white' : 'bg-gray-100 text-charcoal hover:bg-deep-teal hover:text-white' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="relative">
            <input wire:model.live.debounce.300ms="search" type="text" class="pl-10 pr-4 py-3 rounded-2xl border border-gray-300 w-full sm:w-64" placeholder="Search by #ID, Name...">
            <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
        </div>
    </div>

    <!-- Orders List -->
    <section class="space-y-6">
        @forelse ($orders as $order)
            <div class="bg-white rounded-3xl shadow p-6">
                <!-- Card Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <div class="text-xl font-poppins font-bold">#{{ $order->id }}</div>
                        <div class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</div>
                        <div>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-gray-200 text-gray-800',
                                    'preparing' => 'bg-olive-green/15 text-olive-green',
                                    'out_for_delivery' => 'bg-muted-coral/15 text-muted-coral',
                                    'completed' => 'bg-deep-teal/15 text-deep-teal',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                                $class = $statusClasses[$order->status] ?? $statusClasses['pending'];
                                $label = ucwords(str_replace('_', ' ', $order->status));
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $class }}">
                                {{ $label }}
                            </span>
                        </div>
                    </div>
                    <div class="text-sm text-gray-700">
                        <span class="font-semibold">{{ $order->user->name ?? 'Guest' }}</span>
                        <span class="text-gray-500">·</span>
                        <span>{{ $order->user->email ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-5 grid md:grid-cols-2 gap-4">
                    <!-- Items -->
                    <div class="space-y-2">
                        @foreach($order->items as $item)
                        <div class="flex items-start justify-between text-sm border-b pb-2">
                            <div>
                                <div class="font-semibold">{{ $item->drink_name ?? $item->drink->name ?? 'Unknown' }} × {{ $item->quantity }}</div>
                                @if($item->customizations)
                                    <div class="text-gray-500">{{ $item->customizations }}</div>
                                @endif
                            </div>
                            <div class="font-semibold">${{ number_format($item->unit_price * $item->quantity, 2) }}</div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Summary -->
                    <div class="bg-soft-cream rounded-2xl p-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span>Order Type</span><span class="font-semibold capitalize">{{ $order->order_type }}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-2">
                            <span>Payment</span><span class="font-semibold uppercase">{{ $order->payment_method }}</span>
                        </div>
                        <div class="flex justify-between text-sm border-t pt-2">
                            <span>Total</span><span class="font-bold text-deep-teal">${{ number_format($order->total, 2) }}</span>
                        </div>
                        @if ($order->address_json)
                            <div class="mt-3 text-sm">
                                <div class="font-semibold mb-1">Address / Pickup</div>
                                <div class="text-gray-700">
                                    @if(is_array($order->address_json))
                                        {{ implode(', ', array_filter($order->address_json)) }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        <select wire:change="updateStatus({{ $order->id }}, $event.target.value)" class="rounded-2xl border px-3 py-2 text-sm bg-white border-gray-300">
                             @foreach (['pending', 'preparing', 'out_for_delivery', 'completed', 'cancelled'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                        <button class="bg-deep-teal text-white px-4 py-2 rounded-2xl text-sm font-semibold hover:bg-opacity-90">Update</button>
                    </div>

                    <button wire:click="confirmOrderDeletion({{ $order->id }})" class="px-4 py-2 rounded-2xl border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition text-sm">
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 text-gray-600 text-center shadow">No orders found.</div>
        @endforelse
    </section>
    
    <div class="mt-8">
        {{ $orders->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
     <x-confirmation-modal wire:model="confirmingOrderDeletion">
        <x-slot name="title">Delete Order</x-slot>
        <x-slot name="content">Are you sure you want to delete this order? This cannot be undone.</x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingOrderDeletion', false)">Cancel</x-secondary-button>
            <x-danger-button class="ml-3" wire:click="deleteOrder">Delete Order</x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>

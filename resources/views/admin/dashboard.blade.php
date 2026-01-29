<x-guest-layout>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8 font-poppins">
      <!-- Page heading -->
      <h1 class="font-poppins text-2xl font-bold">Admin Panel — BlendUp</h1>
    
      <!-- Admin quick navigation grid -->
      <div class="grid sm:grid-cols-2 gap-6">
        
        <!-- Link to drinks management page -->
        <a href="{{ route('admin.drinks') }}" class="block p-6 rounded-3xl bg-white shadow hover:shadow-lg transition">
          <h3 class="font-poppins font-bold text-lg">Manage Drinks</h3>
          <p class="text-sm text-gray-600">Create, update, delete drinks</p>
        </a>
    
        <!-- Link to orders management page -->
        <a href="{{ route('admin.orders') }}" class="block p-6 rounded-3xl bg-white shadow hover:shadow-lg transition">
          <h3 class="font-poppins font-bold text-lg">Manage Orders</h3>
          <p class="text-sm text-gray-600">View and update order status</p>
        </a>
      </div>
    </main>
</x-guest-layout>

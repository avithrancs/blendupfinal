<header class="bg-white shadow-lg sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center py-4">

      <!-- Brand: logo + name + tagline -->
      <a href="{{ route('home') }}" class="flex items-center space-x-2">
        <div class="w-10 h-10 bg-deep-teal rounded-full flex items-center justify-center">
          <i class="fas fa-blender text-white text-lg"></i>
        </div>
        <div>
          <span class="text-2xl font-poppins font-bold text-deep-teal block leading-none">BlendUp</span>
          <span class="text-xs text-charcoal font-light">Sip Fresh. Live Fresh.</span>
        </div>
      </a>

      <!-- Desktop navigation -->
      <nav class="hidden md:flex items-center space-x-6">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-deep-teal font-semibold border-b-2 border-deep-teal pb-1' : 'text-charcoal hover:text-deep-teal' }}">Home</a>
        <a href="{{ route('menu') }}" class="text-charcoal hover:text-deep-teal">Menu</a>

        <!-- Cart link -->
        <a href="{{ route('cart') }}" class="text-charcoal hover:text-deep-teal relative group">
          <i class="fas fa-shopping-cart group-hover:text-deep-teal"></i>
          <span class="ml-1">Cart</span>
          <span class="absolute -top-2 -right-2 bg-muted-coral text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
            {{ count(session('cart', [])) }}
          </span>
        </a>

        <!-- Auth-aware links -->
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.drinks') }}" class="text-charcoal hover:text-deep-teal font-medium">Admin</a>
            @else
                <a href="{{ route('dashboard') }}" class="text-charcoal hover:text-deep-teal font-medium">Dashboard</a>
            @endif

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-deep-teal text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition shadow-sm text-sm">
                    Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="bg-deep-teal text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition shadow-sm text-sm font-semibold">
                Login
            </a>
        @endauth
      </nav>

      <!-- Mobile menu toggle button -->
      <button class="md:hidden text-deep-teal focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
        <i class="fas fa-bars text-xl"></i>
      </button>
    </div>

    <!-- Mobile navigation -->
    <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-100 mt-2 pt-2">
      <div class="flex flex-col space-y-3">
        <a href="{{ route('home') }}" class="text-charcoal font-medium">Home</a>
        <a href="{{ route('menu') }}" class="text-charcoal font-medium">Menu</a>
        <a href="{{ route('cart') }}" class="text-charcoal font-medium">Cart ({{ count(session('cart', [])) }})</a>
        
        @auth
            <a href="{{ route('dashboard') }}" class="text-charcoal font-medium">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-left w-full text-charcoal font-medium">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-deep-teal font-medium">Login</a>
        @endauth
      </div>
    </div>
  </div>
</header>

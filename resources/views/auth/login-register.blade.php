<x-guest-layout>
    <div class="grid lg:grid-cols-2 min-h-screen">
      <!-- Left photo -->
      <div class="relative hidden lg:block">
        <img src="https://images.stockcake.com/public/a/e/c/aec92c57-ac9b-4fdf-8fec-7113a63c1a86_large/juice-bar-social-stockcake.jpg"
             alt="" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute top-4 left-4 bg-white/90 text-[#280a3e] font-poppins font-bold px-3 py-1 rounded-full shadow">
          BlendUp
        </div>
      </div>
    
      <!-- Right card -->
      <div class="bg-soft-cream flex items-center justify-center py-14 px-4">
        <div class="w-full max-w-xl">
          <div class="bg-white rounded-[24px] shadow-xl border border-[#efe3bf] p-8 md:p-10">
            <h1 class="font-poppins text-3xl font-bold text-[#280a3e]">Welcome back!</h1>
            <p class="mt-1 text-[15px] text-charcoal/80">Login or create an account to order.</p>
    
            <x-validation-errors class="mb-4" />
    
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif
    
            <form x-data="{ action: '{{ route('login') }}' }" :action="action" method="POST" class="mt-6 space-y-5">
              @csrf
              
              <div>
                <label class="block text-sm font-semibold text-[#280a3e] mb-2">Email</label>
                <input
                  name="email" type="email" autocomplete="email" required
                  value="{{ old('email') }}"
                  class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-deep-teal focus:border-transparent px-4 py-3 placeholder:text-gray-400"
                  placeholder="you@example.com">
              </div>
    
              <!-- Name (for Register) -->
              <div>
                <label class="block text-sm font-semibold text-[#280a3e] mb-2">
                  Full Name <span class="text-xs text-gray-500 font-normal">(for Register)</span>
                </label>
                <input
                  name="name" type="text" autocomplete="name"
                  value="{{ old('name') }}"
                  class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-deep-teal focus:border-transparent px-4 py-3"
                  placeholder="Your name">
              </div>
    
              <div>
                <label class="block text-sm font-semibold text-[#280a3e] mb-2">Password</label>
                <input
                  name="password" type="password" autocomplete="current-password" required
                  class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-deep-teal focus:border-transparent px-4 py-3">
              </div>
    
              <div>
                <div class="flex items-baseline justify-between">
                  <label class="block text-sm font-semibold text-[#280a3e] mb-2">
                    Confirm Password <span class="text-xs text-gray-500 font-normal">(for Register)</span>
                  </label>
                  @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-olive-green hover:underline">Forgot Password?</a>
                  @endif
                </div>
                <input
                  name="password_confirmation" type="password" autocomplete="new-password"
                  class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-deep-teal focus:border-transparent px-4 py-3">
              </div>
    
              <!-- Buttons -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <button type="submit" @click.prevent="action = '{{ route('login') }}'; $nextTick(() => $el.closest('form').submit())"
                  class="h-12 rounded-2xl bg-deep-teal text-white font-semibold hover:bg-opacity-90 transition cursor-pointer">
                  Login
                </button>
                <button type="submit" @click.prevent="action = '{{ route('register') }}'; $nextTick(() => $el.closest('form').submit())"
                  class="h-12 rounded-2xl border-2 border-olive-green text-olive-green font-semibold hover:bg-olive-green hover:text-white transition cursor-pointer">
                  Register
                </button>
              </div>
    
              <div class="pt-2">
                <a href="{{ route('home') }}" class="text-sm text-[#280a3e] hover:underline">← Back to Home</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</x-guest-layout>

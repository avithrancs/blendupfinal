<x-guest-layout>
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl">
            <div class="text-center">
                 <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-muted-coral rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-3xl font-extrabold text-charcoal font-poppins">
                    Join BlendUp
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Create your account to start ordering fresh.
                </p>
            </div>

            <x-validation-errors class="mb-4" />

            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="name" class="sr-only">Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="name" name="name" type="text" autocomplete="name" required class="appearance-none rounded-2xl relative block w-full px-12 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-deep-teal focus:border-deep-teal focus:z-10 sm:text-sm" placeholder="Full Name" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none rounded-2xl relative block w-full px-12 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-deep-teal focus:border-deep-teal focus:z-10 sm:text-sm" placeholder="Email address" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none rounded-2xl relative block w-full px-12 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-deep-teal focus:border-deep-teal focus:z-10 sm:text-sm" placeholder="Password">
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="appearance-none rounded-2xl relative block w-full px-12 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-deep-teal focus:border-deep-teal focus:z-10 sm:text-sm" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="flex items-center">
                        <x-checkbox name="terms" id="terms" required />
                        <div class="ms-2 text-sm text-gray-600">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                @endif

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-deep-teal hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-deep-teal shadow-lg transition">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-arrow-right text-deep-teal text-indigo-50 group-hover:text-white"></i>
                        </span>
                        Register
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-deep-teal hover:underline">
                        Log in
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>

<x-guest-layout>
    @section('title', 'Create Account')
    
    <div class="space-y-6">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Create your account</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Join FlippDeal and start trading domains today
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Full Name')" required />
                <div class="mt-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <x-text-input id="name" 
                                  class="pl-10" 
                                  type="text" 
                                  name="name" 
                                  :value="old('name')" 
                                  required 
                                  autofocus 
                                  autocomplete="name" 
                                  placeholder="Enter your full name" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email Address')" required />
                <div class="mt-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <x-text-input id="email" 
                                  class="pl-10" 
                                  type="email" 
                                  name="email" 
                                  :value="old('email')" 
                                  required 
                                  autocomplete="username" 
                                  placeholder="Enter your email address" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" required />
                <div class="mt-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <x-text-input id="password" 
                                  class="pl-10" 
                                  type="password"
                                  name="password"
                                  required 
                                  autocomplete="new-password" 
                                  placeholder="Create a strong password" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" required />
                <div class="mt-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <x-text-input id="password_confirmation" 
                                  class="pl-10" 
                                  type="password"
                                  name="password_confirmation" 
                                  required 
                                  autocomplete="new-password" 
                                  placeholder="Confirm your password" />
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Terms and Privacy -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="terms" 
                           type="checkbox" 
                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-800" 
                           required>
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="text-gray-600 dark:text-gray-400">
                        I agree to the 
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-medium">Terms of Service</a> 
                        and 
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-medium">Privacy Policy</a>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <x-primary-button class="w-full justify-center" size="lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    {{ __('Create Account') }}
                </x-primary-button>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors duration-150">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>

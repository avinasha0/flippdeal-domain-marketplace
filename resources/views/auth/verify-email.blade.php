<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <!-- Logo Section -->
        <div class="mb-8">
            <a href="/" class="flex items-center space-x-3 group">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-200">
                    <span class="text-white font-bold text-xl">F</span>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">FlippDeal</span>
            </a>
        </div>

        <!-- Verification Card -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-xl overflow-hidden sm:rounded-2xl">
            <div class="px-6 py-8">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/20 mb-4">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Verify Your Email</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        We've sent a 6-digit verification code to<br>
                        <span class="font-medium text-blue-600 dark:text-blue-400">{{ session('pending_user.email') }}</span>
                    </p>
                </div>

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.verify-email') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="verification_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Verification Code
                        </label>
                        <input 
                            id="verification_code" 
                            name="verification_code" 
                            type="text" 
                            maxlength="6"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-center text-2xl font-mono tracking-widest"
                            placeholder="000000"
                            required 
                            autofocus
                        >
                        @error('verification_code')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            disabled
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 opacity-50 cursor-not-allowed"
                        >
                            Verify Email & Complete Registration
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Didn't receive the code?
                    </p>
                    <form method="POST" action="{{ route('register.resend-verification') }}" class="mt-2">
                        @csrf
                        <button 
                            type="submit" 
                            class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors duration-200"
                        >
                            Resend Verification Code
                        </button>
                    </form>
                </div>

                <div class="mt-6 text-center">
                    <a 
                        href="{{ route('register') }}" 
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200"
                    >
                        ← Back to Registration
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus and format verification code input
        document.getElementById('verification_code').addEventListener('input', function(e) {
            // Only allow numbers
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            // Enable/disable submit button based on input length
            const submitButton = document.querySelector('button[type="submit"]');
            if (e.target.value.length === 6) {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                submitButton.classList.add('hover:from-blue-700', 'hover:to-purple-700');
            } else {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.classList.remove('hover:from-blue-700', 'hover:to-purple-700');
            }
        });
    </script>
</x-guest-layout>
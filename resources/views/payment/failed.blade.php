<x-layouts.app>
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-black/40">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gray-900 py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-red-500/20 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100/10 mb-6">
                    <svg class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-white mb-2">Payment Failed</h2>
                <p class="text-gray-400 mb-6">Verification failed or payment was cancelled.</p>

                <a href="{{ url('/') }}"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Try Again
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
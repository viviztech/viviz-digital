<x-layouts.app>
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-black/40">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gray-900 py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-emerald-500/20 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100/10 mb-6">
                    <svg class="h-10 w-10 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-white mb-2">Payment Successful!</h2>
                <p class="text-gray-400 mb-6">Thank you for your purchase.</p>

                <div class="bg-gray-800 rounded p-4 mb-6 text-left">
                    <p class="text-sm text-gray-400 mb-1">Order ID</p>
                    <p class="text-white font-mono text-sm break-all">{{ $order->order_number }}</p>
                </div>

                @if($order->downloadToken)
                    <a href="{{ route('download.file', $order->downloadToken->token) }}" Download Asset </a>
                @endif

                    <div class="space-y-3">
                        <a href="{{ route('customer.library') }}"
                            class="block text-indigo-400 hover:text-indigo-300 text-sm font-medium">
                            Go to My Library
                        </a>
                        <a href="{{ url('/') }}" class="block text-gray-400 hover:text-white text-sm">
                            Continue Shopping
                        </a>
                    </div>
            </div>
        </div>
    </div>
</x-layouts.app>
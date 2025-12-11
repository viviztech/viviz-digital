<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div
            class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Current Balance</h3>
            <div class="mt-4 flex items-baseline">
                <span class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 tracking-tight">
                    {{ $currencySymbol }}{{ number_format($balance / 100, 2) }}
                </span>
                <span class="ml-1 text-xl text-gray-500 dark:text-gray-400">INR</span>
            </div>
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                Earnings from your sales and other credits.
            </p>
        </div>

        <!-- Add Funds Card -->
        <div
            class="md:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add Funds</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Top up your wallet securely via Razorpay.</p>

            <div class="max-w-md">
                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount
                    (INR)</label>
                <div class="mt-2 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">₹</span>
                    </div>
                    <input type="number" wire:model="amountToAdd" id="amount"
                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-white"
                        placeholder="0.00" min="1" step="1">
                </div>
                @error('amountToAdd') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                <button type="button" wire:click="initiateTopUp" wire:loading.attr="disabled"
                    class="mt-4 w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50">
                    <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Proceed to Pay
                </button>
            </div>
        </div>
    </div>

    {{ $this->table }}

    <!-- Razorpay Scripts -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('init-razorpay', (event) => {
                // Filament usually wraps events in an array, so accessing data might be event[0] or event directly depending on version
                // For safety, let's treat 'event' as the data object if not array
                const data = Array.isArray(event) ? event[0] : event;

                const options = {
                    key: data.key,
                    amount: data.amount,
                    currency: "INR",
                    name: data.name,
                    description: data.description,
                    image: "/favicon.ico",
                    order_id: data.order_id,
                    handler: function (response) {
                        @this.dispatch('payment-success', { response: response });
                    },
                    prefill: {
                        name: data.prefill.name,
                        email: data.prefill.email
                    },
                    theme: {
                        color: data.theme_color
                    }
                };

                const rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (response) {
                    new FilamentNotification()
                        .title('Payment Failed')
                        .body(response.error.description)
                        .danger()
                        .send();
                });
                rzp1.open();
            });
        });
    </script>
</x-filament-panels::page>
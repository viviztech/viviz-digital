<x-layouts.app>
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-black/40">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Complete your purchase
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gray-900 py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-white/10">

                <!-- Product Summary -->
                <div class="mb-6">
                    <div class="flex items-center space-x-4">
                        @if($product->preview_url)
                            <img src="{{ Storage::url($product->preview_url) }}" alt="{{ $product->name }}"
                                class="h-16 w-16 object-cover rounded-md">
                        @else
                            <div class="h-16 w-16 bg-gray-700 rounded-md flex items-center justify-center">
                                <span class="text-xs text-gray-400">No Img</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-medium text-white">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-400">{{ $product->shop->name }}</p>
                        </div>
                        <div class="ml-auto">
                            <span class="text-xl font-bold text-white">{{ $order->formatted_amount }}</span>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-800 mb-6">

                <!-- Wallet Payment Option -->
                <div class="mb-6 bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-300">Your Wallet Balance</span>
                        <span
                            class="text-lg font-bold text-white">{{ \App\Models\Setting::get('currency_symbol', '₹') . number_format($walletBalance / 100, 2) }}</span>
                    </div>

                    @if($walletBalance >= $order->amount)
                        <form action="{{ route('payment.wallet', $order) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                Pay with Wallet
                            </button>
                        </form>
                    @else
                        <button disabled
                            class="w-full flex justify-center py-3 px-4 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-400 bg-gray-800 cursor-not-allowed opacity-50">
                            Insufficient Wallet Balance
                        </button>
                        <div class="mt-2 text-center">
                            <a href="/my-dashboard/wallet" class="text-xs text-indigo-400 hover:text-indigo-300"
                                target="_blank">Add Funds +</a>
                        </div>
                    @endif
                </div>

                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-2 bg-gray-900 text-sm text-gray-400">
                            Or pay with Card / UPI
                        </span>
                    </div>
                </div>

                @if(!empty($razorpayOrderId) && !empty($razorpayKey))
                    <form action="{{ route('payment.callback') }}" method="POST" id="razorpay-form">
                        @csrf
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                        <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                        <button id="rzp-button1"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Pay with Razorpay
                        </button>
                        <p class="mt-3 text-center text-xs text-gray-500">
                            Secured by Razorpay
                        </p>
                    </form>
                @else
                    <div class="bg-red-900/20 border border-red-500/50 rounded-md p-4 text-center">
                        <p class="text-sm text-red-400">Online Payment Temporarily Unavailable</p>
                        <p class="text-xs text-gray-400 mt-1">Please use your Wallet balance.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        @if(!empty($razorpayOrderId) && !empty($razorpayKey))
            var options = {
                "key": "{{ $razorpayKey }}",
                "amount": "{{ $order->amount }}",
                "currency": "USD",
                "name": "{{ \App\Models\Setting::get('site_name', 'AuraAssets') }}",
                "description": "Purchase {{ $product->name }}",
                "image": "https://dummyimage.com/128x128/6366f1/ffffff&text=A", // Replace with actual logo
                "order_id": "{{ $razorpayOrderId }}",
                "handler": function (response) {
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                    document.getElementById('razorpay_signature').value = response.razorpay_signature;
                    document.getElementById('razorpay-form').submit();
                },
                "prefill": {
                    "name": "{{ auth()->user()->name }}",
                    "email": "{{ auth()->user()->email }}",
                    "contact": ""
                },
                "notes": {
                    "address": "Razorpay Corporate Office"
                },
                "theme": {
                    "color": "#4f46e5"
                }
            };
            var rzp1 = new Razorpay(options);

            document.getElementById('rzp-button1').onclick = function (e) {
                rzp1.open();
                e.preventDefault();
            }
        @endif
    </script>
</x-layouts.app>
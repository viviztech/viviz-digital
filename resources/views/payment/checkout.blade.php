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

                <form action="{{ route('payment.callback') }}" method="POST" id="razorpay-form">
                    @csrf
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                    <button id="rzp-button1"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Pay Now
                    </button>
                    <p class="mt-3 text-center text-xs text-gray-500">
                        Secured by Razorpay
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
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

        // Auto open on load (optional, better UX might be explicit click)
        // rzp1.open();
    </script>
</x-layouts.app>
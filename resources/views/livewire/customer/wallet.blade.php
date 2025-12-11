<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Setting;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

new #[Layout('components.layouts.app')] class extends Component {
    public $transactions = [];
    public $balance = 0;
    public $currencySymbol = '₹';
    public $amountToAdd = 100; // Default 100

    public function mount()
    {
        if (!Auth::check()) {
            return $this->redirect('/login', navigate: true);
        }

        $user = Auth::user();

        // Ensure wallet exists
        if (!$user->wallet) {
            $user->wallet()->create([
                'balance' => 0,
                'currency' => 'INR'
            ]);
            $user->refresh();
        }

        $this->refreshData();
        $this->currencySymbol = Setting::get('currency_symbol', '₹');
    }

    public function refreshData()
    {
        $user = Auth::user();
        if ($user->wallet) {
            $this->balance = $user->wallet->balance;
            $this->transactions = $user->wallet->transactions()->latest()->take(20)->get();
        }
    }

    public function formatAmount($amount)
    {
        return number_format($amount / 100, 2);
    }

    public function initiateTopUp()
    {
        $this->validate([
            'amountToAdd' => 'required|numeric|min:1|max:50000',
        ]);

        $keyId = Setting::get('razorpay_key_id');
        $keySecret = Setting::get('razorpay_key_secret');

        if (!$keyId || !$keySecret) {
            $this->dispatch('notify', message: 'Payment gateway configuration missing.', type: 'error');
            return;
        }

        try {
            $api = new Api($keyId, $keySecret);
            
            // Amount in paise
            $amountInPaise = $this->amountToAdd * 100;

            $orderData = [
                'receipt'         => 'rcptid_' . time() . '_' . Auth::id(),
                'amount'          => $amountInPaise,
                'currency'        => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $api->order->create($orderData);
            
            $this->dispatch('init-razorpay', 
                key: $keyId,
                amount: $amountInPaise,
                order_id: $razorpayOrder['id'],
                name: Setting::get('site_name', 'AuraAssets'),
                description: 'Wallet Top-up',
                prefill: [
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                theme_color: '#6366f1' // Indigo-500
            );

        } catch (\Exception $e) {
            Log::error('Razorpay Init Error: ' . $e->getMessage());
            $this->dispatch('notify', message: 'Could not initiate payment. Please try again.', type: 'error');
        }
    }

    #[On('payment-success')]
    public function verifyTopUp($response)
    {
        $keyId = Setting::get('razorpay_key_id');
        $keySecret = Setting::get('razorpay_key_secret');
        
        $api = new Api($keyId, $keySecret);

        try {
            // Verify signature
            $attributes = [
                'razorpay_order_id' => $response['razorpay_order_id'],
                'razorpay_payment_id' => $response['razorpay_payment_id'],
                'razorpay_signature' => $response['razorpay_signature']
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Fetch payment details to get exact amount
            $payment = $api->payment->fetch($response['razorpay_payment_id']);
            $amountInPaise = $payment->amount;

            // Credit Wallet
            Auth::user()->wallet->credit(
                $amountInPaise, 
                'Wallet Top-up via Razorpay', 
                $response['razorpay_payment_id'],
                'success'
            );

            $this->amountToAdd = 100; // Reset
            $this->refreshData();
            $this->dispatch('notify', message: 'Funds added successfully!', type: 'success');

        } catch (\Exception $e) {
            Log::error('Razorpay Verification Error: ' . $e->getMessage());
            $this->dispatch('notify', message: 'Payment verification failed.', type: 'error');
        }
    }
}; ?>

<div class="min-h-screen bg-deep-void py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-bold leading-7 text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400 sm:text-4xl sm:truncate font-display">
                    My Wallet
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    Manage your balance and view transaction history
                </p>
            </div>
             <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="/my-dashboard" wire:navigate class="inline-flex items-center px-4 py-2 border border-white/10 rounded-lg shadow-sm text-sm font-medium text-gray-300 bg-surface-elevated hover:bg-white/5 hover:text-white transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neon-purple/50">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            
            <!-- Balance & Top-up Card -->
            <div class="md:col-span-1">
                <div class="bg-surface-elevated border border-white/10 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                    <!-- Glow Effects -->
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-green-500/20 rounded-full blur-3xl group-hover:bg-green-500/30 transition-all duration-500"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-neon-purple/20 rounded-full blur-3xl group-hover:bg-neon-purple/30 transition-all duration-500"></div>
                    
                    <div class="relative z-10">
                        <h3 class="text-sm font-medium text-green-400 uppercase tracking-wider">Available Balance</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-4xl font-extrabold text-white tracking-tight font-display">
                                {{ $currencySymbol }}{{ $this->formatAmount($balance) }}
                            </span>
                        </div>

                        <div class="mt-8 pt-6 border-t border-white/10">
                            <label for="amount" class="block text-sm font-medium text-gray-300 mb-2">Add Funds</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">{{ $currencySymbol }}</span>
                                </div>
                                <input type="number" wire:model="amountToAdd" id="amount" 
                                    class="focus:ring-green-500 focus:border-green-500 block w-full pl-8 pr-12 py-3 sm:text-sm border-white/10 rounded-lg bg-black/20 text-white placeholder-gray-500 transition-all focus:bg-black/40" 
                                    placeholder="100.00" min="1" step="1">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">INR</span>
                                </div>
                            </div>
                            @error('amountToAdd') <span class="text-red-400 text-xs mt-2 block">{{ $message }}</span> @enderror

                            <button type="button" wire:click="initiateTopUp" wire:loading.attr="disabled"
                                class="mt-4 w-full flex justify-center items-center px-4 py-3 border border-transparent text-sm font-bold rounded-lg shadow-lg text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Proceed to Pay
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="md:col-span-2">
                <div class="bg-surface-elevated border border-white/10 rounded-2xl shadow-xl overflow-hidden flex flex-col h-full">
                    <div class="px-6 py-5 border-b border-white/10 bg-white/5">
                        <h3 class="text-lg font-medium text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Recent Transactions
                        </h3>
                    </div>
                    
                    @if($transactions->isEmpty())
                        <div class="flex-1 flex flex-col items-center justify-center p-12 text-center text-gray-500 min-h-[300px]">
                            <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-400">No transactions yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Your wallet history will appear here.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-white/5 overflow-y-auto max-h-[600px] custom-scrollbar">
                            @foreach($transactions as $transaction)
                                <li class="px-6 py-4 hover:bg-white/5 transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                @if($transaction->type === 'credit')
                                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-500/10 text-green-400 ring-1 ring-green-500/20 group-hover:bg-green-500/20 transition-colors">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                                        </svg>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-500/10 text-red-400 ring-1 ring-red-500/20 group-hover:bg-red-500/20 transition-colors">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-white group-hover:text-neon-purple transition-colors">
                                                    {{ $transaction->description ?? ucfirst($transaction->type) }}
                                                    @if(isset($transaction->meta['order_id']))
                                                        <span class="ml-2 text-xs px-2 py-0.5 rounded bg-white/5 text-gray-400">#{{ substr($transaction->meta['order_id'], -6) }}</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    {{ $transaction->created_at->format('M d, Y • h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="text-sm font-bold font-mono tracking-wide {{ $transaction->type === 'credit' ? 'text-green-400' : 'text-white' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ $currencySymbol }}{{ $this->formatAmount($transaction->amount) }}
                                            </span>
                                            <span class="text-xs text-gray-500 mt-0.5">
                                                Bal: {{ $currencySymbol }}{{ $this->formatAmount($transaction->closing_balance) }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Razorpay Scripts -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            Livewire.on('init-razorpay', (data) => {
                const options = {
                    key: data.key,
                    amount: data.amount,
                    currency: "INR",
                    name: data.name,
                    description: data.description,
                    image: "/favicon.ico", // Or customized logo
                    order_id: data.order_id,
                    handler: function(response) {
                        // Forward response to backend
                        Livewire.dispatch('payment-success', { response: response });
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
                rzp1.on('payment.failed', function(response) {
                    alert('Payment Failed: ' + response.error.description);
                });
                rzp1.open();
            });
        });
    </script>
</div>

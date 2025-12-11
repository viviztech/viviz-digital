<?php

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $user = Auth::user();
        
        return [
            'user' => $user,
            'ordersCount' => $user->orders()->where('status', 'completed')->count(),
            'wishlistCount' => $user->wishlist()->count(),
            'walletBalance' => $user->balance, // Uses the accessor
            'currencySymbol' => \App\Models\Setting::get('currency_symbol', '₹'),
            'recentOrders' => $user->orders()
                ->with(['product', 'product.shop', 'downloadToken'])
                ->where('status', 'completed')
                ->latest()
                ->take(3)
                ->get(),
        ];
    }
}; ?>

<div class="min-h-screen bg-deep-void py-12">
    <div class="container mx-auto px-6">
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-display font-bold text-white mb-2">Welcome back, {{ $user->name }}! 👋</h1>
            <p class="text-gray-400">Manage your digital assets and account details.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Wallet Stat (Premium Focus) -->
            <div class="relative overflow-hidden rounded-2xl bg-surface-elevated border border-white/10 group hover:border-green-500/50 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-transparent opacity-50 group-hover:opacity-100 transition-opacity"></div>
                <div class="absolute -right-6 -top-6 bg-green-500/20 w-24 h-24 rounded-full blur-2xl group-hover:bg-green-500/30 transition-all"></div>
                
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-green-500/10 rounded-lg text-green-400 group-hover:text-green-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-green-400/80 uppercase tracking-wider">Available Funds</span>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-3xl font-display font-bold text-white tracking-tight">
                            {{ $currencySymbol }}{{ number_format($walletBalance / 100, 2) }}
                        </h3>
                    </div>

                    <a href="{{ route('customer.wallet') }}" class="inline-flex items-center text-sm font-bold text-green-400 group-hover:text-green-300 transition-colors">
                        Add Funds
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Library Stat -->
            <div class="relative overflow-hidden rounded-2xl bg-surface-elevated border border-white/10 group hover:border-neon-purple/50 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-neon-purple/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-neon-purple/10 rounded-lg text-neon-purple">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">My Library</span>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-3xl font-display font-bold text-white tracking-tight">{{ $ordersCount }}</h3>
                        <p class="text-sm text-gray-400">Purchased Items</p>
                    </div>

                    <a href="{{ route('customer.library') }}" class="inline-flex items-center text-sm font-medium text-white/60 hover:text-white transition-colors">
                        View Collection
                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Wishlist Stat -->
            <div class="relative overflow-hidden rounded-2xl bg-surface-elevated border border-white/10 group hover:border-neon-pink/50 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-neon-pink/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-neon-pink/10 rounded-lg text-neon-pink">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Wishlist</span>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-3xl font-display font-bold text-white tracking-tight">{{ $wishlistCount }}</h3>
                        <p class="text-sm text-gray-400">Saved Items</p>
                    </div>

                    <a href="{{ route('customer.wishlist') }}" class="inline-flex items-center text-sm font-medium text-white/60 hover:text-white transition-colors">
                        View Wishlist
                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Profile/Account Stat -->
            <div class="relative overflow-hidden rounded-2xl bg-surface-elevated border border-white/10 group hover:border-neon-teal/50 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-neon-teal/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-neon-teal/10 rounded-lg text-neon-teal">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Account</span>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-white truncate">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-400 truncate">{{ $user->email }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                         <span class="inline-flex items-center px-2 py-1 rounded-md bg-white/5 text-xs text-gray-300 border border-white/10">
                            Member
                        </span>
                        <!-- Placeholder for future Edit Profile -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white">Recent Purchases</h2>
                <a href="{{ route('customer.library') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">View All</a>
            </div>

            @if($recentOrders->isNotEmpty())
                <div class="bg-surface-elevated border border-white/10 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white/5 text-gray-400 text-sm">
                                <tr>
                                    <th class="px-6 py-4 font-medium">Product</th>
                                    <th class="px-6 py-4 font-medium">Date</th>
                                    <th class="px-6 py-4 font-medium">Amount</th>
                                    <th class="px-6 py-4 font-medium text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($order->product->preview_url)
                                                    <img src="{{ Storage::url($order->product->preview_url) }}" class="w-10 h-10 rounded object-cover bg-black/50" alt="">
                                                @else
                                                    <div class="w-10 h-10 rounded bg-white/10 flex items-center justify-center text-xs text-gray-500">N/A</div>
                                                @endif
                                                <div>
                                                    <div class="text-white font-medium">{{ $order->product->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $order->product->shop->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-400 text-sm">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-white font-medium">
                                            {{ $order->formatted_amount }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($order->downloadToken && $order->downloadToken->isValid())
                                                <a href="{{ route('download.file', $order->downloadToken->token) }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-gray-600 text-sm">Expired</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-surface-elevated border border-white/10 rounded-xl p-12 text-center">
                     <div class="w-16 h-16 mx-auto mb-4 bg-white/5 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">No recent purchases</h3>
                    <p class="text-gray-400 mb-6">Start exploring our marketplace to find amazing digital assets.</p>
                    <a href="/" class="btn-glow inline-flex items-center px-6 py-2.5 bg-neon-purple text-white font-bold rounded-lg hover:brightness-110 transition-all font-display">
                        Explore Marketplace
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Library Stat -->
            <div class="bg-surface-elevated border border-white/10 rounded-xl p-6 hover:border-neon-purple/50 transition-colors group relative overflow-hidden">
                <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-neon-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="text-gray-400 text-sm font-medium mb-1">My Library</div>
                    <div class="text-3xl font-bold text-white mb-4">{{ $ordersCount }} Items</div>
                    <a href="{{ route('customer.library') }}" class="inline-flex items-center text-neon-purple hover:text-white transition-colors text-sm font-medium">
                        View Library
                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Wishlist Stat -->
            <div class="bg-surface-elevated border border-white/10 rounded-xl p-6 hover:border-neon-pink/50 transition-colors group relative overflow-hidden">
                <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-neon-pink" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="text-gray-400 text-sm font-medium mb-1">Wishlist</div>
                    <div class="text-3xl font-bold text-white mb-4">{{ $wishlistCount }} Items</div>
                    <a href="{{ route('customer.wishlist') }}" class="inline-flex items-center text-neon-pink hover:text-white transition-colors text-sm font-medium">
                        View Wishlist
                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Profile/Account Stat -->
            <div class="bg-surface-elevated border border-white/10 rounded-xl p-6 hover:border-neon-teal/50 transition-colors group relative overflow-hidden">
                 <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-neon-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="text-gray-400 text-sm font-medium mb-1">Account</div>
                    <div class="text-3xl font-bold text-white mb-4">Profile</div>
                    <!-- Assuming a profile edit page might exist or will exist, for now basic info loop or just placeholder link -->
                    <div class="text-sm text-gray-500 mb-2">{{ $user->email }}</div>
                    
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

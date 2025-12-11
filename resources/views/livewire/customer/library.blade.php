<?php

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'orders' => Order::with(['product', 'product.shop', 'downloadToken'])
                ->where('user_id', Auth::id())
                ->where('status', 'completed')
                ->latest()
                ->paginate(12),
        ];
    }
}; ?>

<div class="min-h-screen bg-deep-void py-12">
    <div class="container mx-auto px-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-display font-bold text-white">My Library</h1>
            <a href="/" class="text-gray-400 hover:text-white transition-colors">Back to Market</a>
        </div>

        @if($orders->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($orders as $order)
                    <div class="bg-surface-elevated border border-white/10 rounded-xl overflow-hidden hover:border-neon-purple/50 transition-colors group">
                        
                        <!-- Product Preview -->
                        <div class="aspect-video relative bg-black/50">
                            @if($order->product->preview_url)
                                <img src="{{ Storage::url($order->product->preview_url) }}" 
                                     alt="{{ $order->product->name }}" 
                                     class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-500">
                                    No Preview
                                </div>
                            @endif
                            
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 bg-black/60 text-white text-xs rounded border border-white/10">
                                    {{ $order->formatted_amount }}
                                </span>
                            </div>
                        </div>
                        <div class="p-5 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-white">{{ $order->product->name }}</h4>
                                <p class="text-xs text-gray-400">Order #{{ $order->id }} • {{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="px-5 pb-5 flex items-center gap-4">
                            <span class="text-neon-purple font-bold">{{ $order->product->formatted_price }}</span>
                            <a href="{{ route('orders.invoice', $order) }}" 
                                class="text-xs px-3 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-gray-300 transition-colors flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Invoice
                            </a>
                            <span class="text-xs px-2 py-1 {{ $order->status === 'paid' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' }} rounded">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-5 pt-0">
                            <h3 class="text-lg font-bold text-white mb-1 line-clamp-1">{{ $order->product->name }}</h3>
                            <p class="text-sm text-gray-400 mb-4">Sold by {{ $order->product->shop->name }}</p>
                            
                            <div class="flex items-center justify-between mt-4">
                                <span class="text-xs text-gray-500">
                                    Purchased {{ $order->created_at->format('M d, Y') }}
                                </span>

                                @if($order->downloadToken && $order->downloadToken->isValid())
                                    <a href="{{ route('download.file', $order->downloadToken->token) }}" 
                                       class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                @else
                                    <button disabled class="px-4 py-2 bg-gray-700 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
                                        Expired
                                    </button>
                                @endif
                            </div>
                             
                             @if($order->downloadToken)
                                <div class="mt-3 text-xs text-gray-500 flex justify-between">
                                     <span>Downloads: {{ $order->downloadToken->download_count }} / {{ $order->downloadToken->max_downloads }}</span>
                                     <span>Expires: {{ $order->downloadToken->expires_at->diffForHumans() }}</span>
                                </div>
                             @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-24 bg-surface-elevated/50 rounded-2xl border border-white/5">
                <div class="w-20 h-20 mx-auto mb-6 bg-surface-elevated rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">Your library is empty</h2>
                <p class="text-gray-400 mb-8 max-w-sm mx-auto">Once you purchase digital assets, they will appear here for instant download.</p>
                <a href="/" class="btn-glow inline-flex items-center px-6 py-3 bg-neon-purple text-white font-bold rounded-lg hover:brightness-110 transition-all">
                    Browse Marketplace
                </a>
            </div>
        @endif
    </div>
</div>

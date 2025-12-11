<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function with()
    {
        return [
            'products' => Auth::user()->wishlistProducts()->with(['shop', 'category'])->latest('wishlists.created_at')->paginate(12),
        ];
    }

    public function removeFromWishlist($productId)
    {
        Auth::user()->wishlistProducts()->detach($productId);
        // Refresh is automatic due to reactivity or navigate
        // If not, we can dispatch
    }
}; ?>

<div class="min-h-screen bg-deep-void py-12">
    <div class="container mx-auto px-6">
        <h1 class="text-3xl font-display font-bold text-white mb-2">My Wishlist</h1>
        <p class="text-gray-400 mb-8">Saved items for later</p>

        @if($products->isEmpty())
            <div class="bg-surface-elevated rounded-2xl border border-white/5 p-12 text-center">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Your wishlist is empty</h3>
                <p class="text-gray-400 mb-6 max-w-md mx-auto">Explore our marketplace and save your favorite digital assets
                    here.</p>
                <a href="/" wire:navigate
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-neon-purple to-neon-pink text-white font-bold rounded-xl hover:shadow-glow transition-all transform hover:scale-105">
                    Explore Assets
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div
                        class="group relative bg-surface-elevated rounded-2xl overflow-hidden border border-white/5 hover:border-neon-purple/50 transition-all hover:shadow-glow/20">
                        <!-- Image -->
                        <div class="aspect-video relative overflow-hidden bg-surface">
                            <img src="{{ Storage::url($product->preview_url) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                            <!-- Remove Button -->
                            <button wire:click="removeFromWishlist({{ $product->id }})"
                                class="absolute top-3 right-3 p-2 bg-black/50 hover:bg-red-500/80 backdrop-blur-md rounded-full text-white transition-all z-10">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" stroke="none">
                                    <path
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="absolute bottom-4 left-4 right-4">
                                    <a href="#"
                                        class="block w-full text-center py-2 bg-white text-black font-bold rounded-lg hover:bg-neon-pink hover:text-white transition-colors">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3
                                        class="font-bold text-white text-lg line-clamp-1 group-hover:text-neon-pink transition-colors">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-sm text-gray-400">{{ $product->shop->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-neon-pink">{{ $product->formatted_price }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
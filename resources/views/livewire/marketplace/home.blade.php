<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use function Livewire\Volt\{state, computed, mount};

state(['products' => [], 'categories' => [], 'selectedCategory' => 'all', 'wishlistIds' => [], 'search' => '', 'expandedTerms' => []]);

mount(function () {
    $this->categories = Category::where('is_active', true)->orderBy('sort_order')->get();

    $this->products = Product::with(['shop', 'category'])
        ->active()
        ->latest()
        ->take(24)
        ->get();

    if (Auth::check()) {
        $this->wishlistIds = Auth::user()->wishlistProducts()->pluck('products.id')->toArray();
    }
});

$updateSearch = #[On('update-search')] function ($search) {
    $this->search = $search;
    $this->expandedTerms = []; // Reset
};

$updateCategory = #[On('update-category')] function ($category) {
    $this->selectedCategory = $category;
};

$featuredProducts = computed(function () {
    return $this->products->where('is_featured', true)->take(\App\Models\Setting::get('featured_products_count', 6));
});

$filteredProducts = computed(function () {
    $items = $this->products;

    // Filter by Category
    if ($this->selectedCategory !== 'all') {
        $items = $items->filter(fn($product) => optional($product->category)->slug === $this->selectedCategory);
    }

    // Filter by Search (Smart Semantic Search)
    if (!empty($this->search)) {
        $term = strtolower($this->search);

        // Expand query if not already expanded
        if (empty($this->expandedTerms)) {
            try {
                $service = new \App\Services\GeminiService();
                $this->expandedTerms = $service->expandSearchQuery($term);
            } catch (\Exception $e) {
                // Fallback to basic search
                $this->expandedTerms = [$term];
            }
        }

        $searchTerms = array_map('strtolower', $this->expandedTerms);

        $items = $items->filter(function ($product) use ($searchTerms) {
            $name = strtolower($product->name);
            $desc = strtolower($product->description);
            $tags = is_array($product->ai_metadata) ? implode(' ', array_values($product->ai_metadata)) : '';
            $tags = strtolower($tags);

            foreach ($searchTerms as $st) {
                if (str_contains($name, $st) || str_contains($desc, $st) || str_contains($tags, $st)) {
                    return true;
                }
            }
            return false;
        });
    }

    return $items;
});

$filterByCategory = fn(string $slug) => $this->selectedCategory = $slug;

$toggleWishlist = function ($productId) {
    if (!Auth::check()) {
        return $this->redirect('/login', navigate: true);
    }

    $user = Auth::user();
    if (in_array($productId, $this->wishlistIds)) {
        $user->wishlistProducts()->detach($productId);
        $this->wishlistIds = array_diff($this->wishlistIds, [$productId]);
    } else {
        $user->wishlistProducts()->attach($productId);
        $this->wishlistIds[] = $productId;
    }
};

?>

<div class="min-h-screen bg-deep-void">
    <div class="min-h-screen bg-deep-void">
        <!-- Hero Section -->
        <section class="relative overflow-hidden py-24 lg:py-32">
            <!-- Background Effects -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-1/4 -left-1/4 w-1/2 h-1/2 bg-neon-purple/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-neon-pink/20 rounded-full blur-3xl"></div>
            </div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-5xl lg:text-7xl font-display font-bold text-white mb-6 leading-tight">
                        Premium Digital Assets
                        <span
                            class="block bg-gradient-to-r from-neon-purple via-neon-pink to-neon-blue bg-clip-text text-transparent">
                            For Creators
                        </span>
                    </h1>
                    <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto">
                        Discover stunning photos, videos, and templates from talented creators worldwide. Instant
                        downloads,
                        lifetime licenses.
                    </p>

                    <!-- Category Pills -->
                    <div class="flex flex-wrap justify-center gap-3 mb-12">
                        <button wire:click="filterByCategory('all')"
                            class="px-5 py-2 rounded-full font-medium transition-all {{ $selectedCategory === 'all' ? 'bg-gradient-to-r from-neon-purple to-neon-pink text-white shadow-glow' : 'bg-surface-elevated text-gray-300 hover:text-white hover:bg-surface' }}">
                            All Assets
                        </button>

                        @foreach($categories as $category)
                            <button wire:click="filterByCategory('{{ $category->slug }}')"
                                class="px-5 py-2 rounded-full font-medium transition-all {{ $selectedCategory === $category->slug ? 'bg-gradient-to-r from-neon-purple to-neon-pink text-white shadow-glow' : 'bg-surface-elevated text-gray-300 hover:text-white hover:bg-surface' }}">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Section -->
        @if($this->featuredProducts->isNotEmpty())
            <section class="container mx-auto px-6 pb-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-display font-bold text-white">Featured Assets</h2>
                    <a href="/featured" class="text-neon-purple hover:text-neon-pink transition-colors font-medium">
                        View All →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($this->featuredProducts as $product)
                        <article class="bento-item group">
                            <a href="{{ route('products.show', $product) }}" wire:navigate class="block w-full h-full relative">
                                <!-- Wishlist Button -->
                                <button wire:click.stop="toggleWishlist({{ $product->id }})"
                                    class="absolute top-3 right-3 p-2 rounded-full transition-all hover:scale-110 focus:outline-none z-10 {{ in_array($product->id, $wishlistIds) ? 'bg-neon-pink/20 text-neon-pink' : 'bg-black/50 text-white hover:bg-neon-pink/80' }}">
                                    <svg class="w-5 h-5 {{ in_array($product->id, $wishlistIds) ? 'fill-current' : 'fill-none stroke-current' }}"
                                        viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                                <img src="{{ $product->preview_url ? Storage::url($product->preview_url) : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600' }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy" />
                                <div class="overlay">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="px-3 py-1 bg-neon-purple/80 text-white text-xs font-semibold rounded-full">
                                            {{ $product->category?->name ?? 'Uncategorized' }}
                                        </span>
                                        @if($product->average_rating > 0)
                                            <span
                                                class="px-2 py-1 bg-black/60 backdrop-blur-md text-white text-xs font-bold rounded-full flex items-center gap-1">
                                                <span class="text-neon-pink">★</span> {{ $product->average_rating }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-semibold text-white mb-1">{{ $product->name }}</h3>
                                    <p class="text-neon-pink font-bold">{{ $product->formatted_price }}</p>
                                    <p class="text-gray-400 text-sm mt-1">by {{ $product->shop->name }}</p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Main Masonry Grid -->
        <section class="container mx-auto px-6 pb-24">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-display font-bold text-white">Latest Assets</h2>
                <div class="flex items-center gap-4">
                    @if(!empty($search) && count($expandedTerms) > 1)
                        <div class="hidden md:block text-sm text-gray-400">
                            <span class="text-neon-purple">Smart Search:</span>
                            Found results related to
                            @foreach(array_slice($expandedTerms, 0, 3) as $term)
                                <span class="text-gray-300 font-medium">"{{ $term }}"</span>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                            @if(count($expandedTerms) > 3) & more... @endif
                        </div>
                    @endif
                    <select
                        class="px-4 py-2 bg-surface-elevated border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-neon-purple/50">
                        <option>Most Recent</option>
                        <option>Most Popular</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <!-- Bento Grid -->
            <div class="bento-grid">
                @forelse($this->filteredProducts as $product)
                    <article class="bento-item group" wire:key="product-{{ $product->id }}">
                        <a href="{{ route('products.show', $product) }}" wire:navigate class="block w-full h-full relative">
                            <!-- Wishlist Button -->
                            <button wire:click.stop="toggleWishlist({{ $product->id }})"
                                class="absolute top-3 right-3 p-2 rounded-full transition-all hover:scale-110 focus:outline-none z-10 {{ in_array($product->id, $wishlistIds) ? 'bg-neon-pink/20 text-neon-pink' : 'bg-black/50 text-white hover:bg-neon-pink/80' }}">
                                <svg class="w-5 h-5 {{ in_array($product->id, $wishlistIds) ? 'fill-current' : 'fill-none stroke-current' }}"
                                    viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                            <img src="{{ $product->preview_url ? Storage::url($product->preview_url) : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600' }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                loading="lazy" />

                            <!-- Info Overlay -->
                            <div class="overlay">
                                <div class="flex items-center gap-2 mb-3">
                                    <span
                                        class="px-3 py-1 bg-neon-purple/80 text-white text-xs font-semibold rounded-full inline-flex items-center gap-1.5">
                                        @if(optional($product->category)->icon)
                                            <!-- Use dynamic icon if available -->
                                        @endif
                                        {{ $product->category?->name ?? 'Uncategorized' }}
                                    </span>
                                    @if($product->average_rating > 0)
                                        <span
                                            class="px-2 py-1 bg-black/60 backdrop-blur-md text-white text-xs font-bold rounded-full flex items-center gap-1">
                                            <span class="text-neon-pink">★</span> {{ $product->average_rating }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-1 line-clamp-1">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between">
                                    <p class="text-neon-pink font-bold text-lg">{{ $product->formatted_price }}</p>
                                    <p class="text-gray-400 text-sm">{{ $product->downloads_count }} downloads</p>
                                </div>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="col-span-full text-center py-16">
                        <div
                            class="w-24 h-24 mx-auto mb-6 bg-surface-elevated rounded-2xl flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">No assets found</h3>
                        <p class="text-gray-400">Check back later for new content from our creators.</p>
                    </div>
                @endforelse
            </div>

            <!-- Load More -->
            @if($products->count() >= 24)
                <div class="text-center mt-12">
                    <button
                        class="px-8 py-3 bg-surface-elevated border border-white/10 text-white font-semibold rounded-xl hover:bg-surface hover:border-neon-purple/50 transition-all">
                        Load More Assets
                    </button>
                </div>
            @endif
        </section>

    </div>
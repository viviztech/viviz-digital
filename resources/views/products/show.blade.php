<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Breadcrumbs (Optional, nice to have) -->
        <nav class="flex mb-8 text-sm text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="/" class="hover:text-white transition-colors">Home</a></li>
                <li><svg class="flex-shrink-0 h-4 w-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg></li>
                <li><span class="text-gray-300">Products</span></li>
                <li><svg class="flex-shrink-0 h-4 w-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg></li>
                <li class="font-medium text-white truncate max-w-xs">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

            <!-- LEFT COLUMN: Product Content -->
            <div class="lg:col-span-2 space-y-10">

                <!-- Product Preview Image -->
                <div
                    class="relative group rounded-2xl overflow-hidden border border-white/10 shadow-2xl bg-surface-elevated aspect-video sm:aspect-[16/9]">
                    @if($product->preview_url)
                        <img src="{{ Storage::url($product->preview_url) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-500 bg-gray-900">
                            <svg class="w-20 h-20 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <div class="absolute top-4 left-4">
                        <span
                            class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-black/60 text-white backdrop-blur-md border border-white/10 uppercase tracking-wider shadow-lg">
                            {{ $product->type }}
                        </span>
                    </div>
                </div>

                <!-- Product Header & Main Info -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-4xl sm:text-5xl font-display font-bold text-white leading-tight">
                            {{ $product->name }}
                        </h1>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-4">

                            <!-- Shop / Author -->
                            <div class="flex items-center gap-3">
                                @if($product->shop->logo_url)
                                    <img class="h-12 w-12 rounded-full border border-white/20"
                                        src="{{ Storage::url($product->shop->logo_url) }}" alt="{{ $product->shop->name }}">
                                @else
                                    <div
                                        class="h-12 w-12 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold border border-white/20 text-lg">
                                        {{ substr($product->shop->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm text-gray-400">Created by</p>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-base font-medium text-white hover:text-indigo-400 transition-colors cursor-pointer">
                                            {{ $product->shop->name }}
                                        </span>
                                        @if($product->shop->is_verified)
                                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="text-right">
                                <span
                                    class="block text-4xl font-bold bg-gradient-to-r from-neon-purple to-neon-pink bg-clip-text text-transparent">
                                    {{ $product->formatted_price }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Main CTA -->
                    <div
                        class="p-6 bg-surface-elevated rounded-xl border border-white/10 flex flex-col sm:flex-row gap-4 items-center">
                        <div class="flex-1 text-sm text-gray-400">
                            <p><span class="text-white font-medium">Instant Download:</span> Get access to your files
                                immediately after payment.</p>
                            <p class="mt-1"><span class="text-white font-medium">Secure:</span> Validated by Razorpay.
                            </p>
                        </div>
                        <a href="{{ route('payment.initiate', $product) }}"
                            class="w-full sm:w-auto btn-glow px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white text-lg font-bold rounded-xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-2">
                            <span>Buy Now</span>
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Product Description & Specs -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Description -->
                    <div class="md:col-span-2 space-y-6">
                        <h3 class="text-2xl font-bold text-white border-b border-white/10 pb-2">Description</h3>
                        <div class="prose prose-invert prose-lg text-gray-300 max-w-none">
                            <p>{{ $product->description }}</p>
                        </div>
                    </div>

                    <!-- Highlights/Specs -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-white border-b border-white/10 pb-2">Highlights</h3>
                        <ul class="space-y-3 text-sm text-gray-300">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-neon-purple mt-0.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>High Resolution</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-neon-purple mt-0.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Commercial License</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-neon-purple mt-0.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Category: {{ Str::title($product->type) }}</span>
                            </li>
                        </ul>

                        @if($product->ai_metadata)
                            <div class="pt-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Tags</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(json_decode($product->ai_metadata) ?? [] as $tag)
                                        <span
                                            class="px-2 py-1 bg-surface border border-white/5 rounded text-xs text-gray-400">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Product Reviews -->
                <livewire:marketplace.product-reviews :product="$product" />
            </div>

            <!-- RIGHT COLUMN: Sidebar -->
            <div class="space-y-8 lg:sticky lg:top-8">

                <!-- Categories Widget -->
                <div class="bg-surface-elevated rounded-2xl border border-white/10 p-6 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-neon-pink" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Explore Categories
                    </h3>
                    <div class="space-y-2">
                        @foreach($categories as $category)
                            <a href="/?type={{ $category }}"
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-white/5 transition-colors group">
                                <span class="capitalize text-gray-300 group-hover:text-white">{{ $category }}</span>
                                <svg class="w-4 h-4 text-gray-600 group-hover:text-neon-purple transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                        <a href="/"
                            class="flex items-center justify-between p-3 rounded-lg hover:bg-white/5 transition-colors group border-t border-white/5 mt-2">
                            <span class="text-gray-400 group-hover:text-white">View All</span>
                            <svg class="w-4 h-4 text-gray-600 group-hover:text-white transition-colors" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Latest Arrivals Widget -->
                <div class="bg-surface-elevated rounded-2xl border border-white/10 p-6 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Latest Arrivals
                    </h3>
                    <div class="space-y-5">
                        @foreach($recentProducts as $recent)
                            <a href="{{ route('products.show', $recent) }}" class="flex gap-4 group">
                                <div
                                    class="w-20 h-20 flex-shrink-0 bg-gray-800 rounded-lg overflow-hidden border border-white/10">
                                    @if($recent->preview_url)
                                        <img src="{{ Storage::url($recent->preview_url) }}" alt="{{ $recent->name }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-500">No Img
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="text-sm font-bold text-gray-200 truncate group-hover:text-white transition-colors">
                                        {{ $recent->name }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1 truncate">{{ $recent->shop->name }}</p>
                                    <p class="text-sm font-bold text-neon-purple mt-2">{{ $recent->formatted_price }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-layouts.app>
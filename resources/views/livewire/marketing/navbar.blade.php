<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Component;

new class extends Component {
    public $search = '';
    public $categories = [];

    public function mount()
    {
        $this->categories = Category::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function updatedSearch()
    {
        if (Route::is('marketplace.home') || request()->is('/')) {
            $this->dispatch('update-search', search: $this->search);
        }
    }

    public function performSearch()
    {
        if (!Route::is('marketplace.home') && !request()->is('/')) {
            $this->redirect('/?search=' . urlencode($this->search), navigate: true);
        }
    }

    public function filterByCategory($slug)
    {
        if (Route::is('marketplace.home') || request()->is('/')) {
            $this->dispatch('update-category', category: $slug);
        } else {
            $this->redirect('/?type=' . $slug, navigate: true);
        }
    }
}; ?>

<header class="sticky top-0 z-50 backdrop-blur-xl bg-surface/80 border-b border-white/5">
    <nav class="container mx-auto flex items-center justify-between px-6 py-4">
        <!-- Logo -->
        <a href="/" class="group flex items-center gap-3" wire:navigate>
            <div class="relative">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-neon-purple to-neon-pink blur-lg opacity-50 group-hover:opacity-75 transition-opacity">
                </div>
                <svg class="relative w-10 h-10 text-white" viewBox="0 0 40 40" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 2L35 10V30L20 38L5 30V10L20 2Z" stroke="currentColor" stroke-width="2"
                        fill="url(#logo-gradient)" />
                    <defs>
                        <linearGradient id="logo-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:var(--color-neon-purple);stop-opacity:0.5" />
                            <stop offset="100%" style="stop-color:var(--color-neon-pink);stop-opacity:0.5" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <span
                class="text-2xl font-display font-bold bg-gradient-to-r from-neon-purple via-neon-pink to-neon-blue bg-clip-text text-transparent">
                {{ \App\Models\Setting::get('site_name', 'AuraAssets') }}
            </span>
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex gap-8 items-center">
            <!-- Dynamic Explore Dropdown -->
            <div class="relative group" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                    class="flex items-center gap-1 text-sm font-medium text-white hover:text-neon-pink transition-colors focus:outline-none">
                    Explore
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute left-0 mt-2 w-48 bg-surface-elevated rounded-xl shadow-lg border border-white/10 py-1 z-50">
                    <button wire:click="filterByCategory('all'); open = false"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">
                        All Assets
                    </button>
                    @foreach($categories as $category)
                        <button wire:click="filterByCategory('{{ $category->slug }}'); open = false"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <a href="/become-creator"
                class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Creators</a>
            <a href="/licenses" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">License</a>
        </nav>

        <!-- Search Bar -->
        <div class="hidden md:flex flex-1 max-w-xl mx-8">
            <div class="relative w-full group">
                <form wire:submit.prevent="performSearch">
                    <input type="search" wire:model.live.debounce.300ms="search"
                        placeholder="Search photos, videos, templates..."
                        class="w-full px-5 py-3 pl-12 bg-surface-elevated border border-white/10 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-neon-purple/50 focus:border-neon-purple/50 transition-all" />
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-neon-purple transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>
            </div>
        </div>

        <!-- Navigation Items -->
        <div class="flex items-center gap-4">
            <!-- Island: Cart Counter -->
            <button class="relative p-2 text-gray-300 hover:text-white transition-colors group">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span
                    class="absolute -top-1 -right-1 w-5 h-5 bg-neon-pink text-white text-xs font-bold rounded-full flex items-center justify-center">
                    0
                </span>
            </button>

            <!-- Island: User Notifications -->
            <button class="relative p-2 text-gray-300 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-neon-purple rounded-full animate-pulse"></span>
            </button>

            <!-- User Menu -->
            @auth
                <div class="relative group" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="flex items-center gap-2 focus:outline-none">
                        <div
                            class="h-8 w-8 rounded-full bg-gradient-to-br from-neon-purple to-neon-pink flex items-center justify-center text-white font-bold border border-white/20">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-surface-elevated rounded-xl shadow-lg border border-white/10 py-1 z-50">

                        <div class="px-4 py-2 border-b border-white/5">
                            <p class="text-sm text-white font-medium truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>

                        <a href="/my-dashboard" wire:navigate
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">
                            Dashboard
                        </a>
                        <a href="/my-library" wire:navigate
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">
                            My Library
                        </a>
                        <a href="/my-wishlist" wire:navigate
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">
                            My Wishlist
                        </a>

                        @if(auth()->user()->is_vendor)
                            <a href="/creator"
                                class="block px-4 py-2 text-sm text-neon-purple hover:bg-white/5 hover:text-neon-pink">
                                Creator Dashboard
                            </a>
                        @endif

                        @if(auth()->user()->is_admin)
                            <a href="/admin"
                                class="block px-4 py-2 text-sm text-indigo-400 hover:bg-white/5 hover:text-indigo-300">
                                Admin Panel
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/5 hover:text-red-300">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login" wire:navigate
                    class="px-5 py-2 bg-gradient-to-r from-neon-purple to-neon-pink text-white font-semibold rounded-lg hover:shadow-glow transition-all transform hover:scale-105">
                    Sign In
                </a>
            @endauth
        </div>
    </nav>
</header>
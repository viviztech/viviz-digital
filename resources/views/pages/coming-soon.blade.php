<x-layouts.app>
    <div class="min-h-screen flex flex-col items-center justify-center bg-deep-void relative overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 -left-1/4 w-1/2 h-1/2 bg-neon-purple/20 rounded-full blur-3xl animate-pulse">
            </div>
            <div class="absolute bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-neon-pink/20 rounded-full blur-3xl animate-pulse"
                style="animation-duration: 4s;"></div>
        </div>

        <div class="relative z-10 text-center px-4">
            <h1 class="text-6xl md:text-8xl font-display font-bold text-white mb-6">
                Coming <span
                    class="bg-gradient-to-r from-neon-purple to-neon-pink bg-clip-text text-transparent">Soon</span>
            </h1>

            <p class="text-xl md:text-2xl text-gray-400 mb-12 max-w-2xl mx-auto">
                We're crafting something extraordinary. This page is currently under construction.
            </p>

            <a href="/"
                class="btn-glow inline-flex items-center px-8 py-4 bg-surface-elevated border border-white/10 rounded-xl text-white font-semibold transition-all hover:scale-105 hover:bg-surface">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Return Home
            </a>
        </div>
    </div>
</x-layouts.app>
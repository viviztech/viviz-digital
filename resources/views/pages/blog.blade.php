<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-4xl font-extrabold text-white mb-8">Latest from Our Blog</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Blog Post 1 -->
            <article class="flex flex-col space-y-4">
                <div
                    class="aspect-video bg-gray-800 rounded-2xl overflow-hidden border border-white/10 group cursor-pointer">
                    <!-- Placeholder Image -->
                    <div
                        class="w-full h-full bg-gradient-to-br from-indigo-900 to-black flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                        <span class="text-indigo-400 font-bold text-2xl">Design Trends 2024</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3 text-sm text-gray-500 mb-2">
                        <span class="text-neon-purple font-medium">Trends</span>
                        <span>&bull;</span>
                        <span>Oct 12, 2024</span>
                    </div>
                    <h2
                        class="text-2xl font-bold text-white mb-2 hover:text-neon-purple transition-colors cursor-pointer">
                        5 Cyberpunk Design Trends Defining 2024</h2>
                    <p class="text-gray-400">From neon-drenched cityscapes to glitch typography, explore what's shaping
                        the digital art world this year.</p>
                </div>
            </article>

            <!-- Blog Post 2 -->
            <article class="flex flex-col space-y-4">
                <div
                    class="aspect-video bg-gray-800 rounded-2xl overflow-hidden border border-white/10 group cursor-pointer">
                    <!-- Placeholder Image -->
                    <div
                        class="w-full h-full bg-gradient-to-br from-purple-900 to-black flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                        <span class="text-purple-400 font-bold text-2xl">Monetize Your Art</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3 text-sm text-gray-500 mb-2">
                        <span class="text-neon-pink font-medium">Guides</span>
                        <span>&bull;</span>
                        <span>Sep 28, 2024</span>
                    </div>
                    <h2
                        class="text-2xl font-bold text-white mb-2 hover:text-neon-pink transition-colors cursor-pointer">
                        How to Price Your Digital Assets</h2>
                    <p class="text-gray-400">A comprehensive guide for creators on valuing their work and maximizing
                        sales on AuraAssets.</p>
                </div>
            </article>
        </div>

        <div class="mt-16 text-center">
            <button
                class="px-8 py-3 bg-surface-elevated border border-white/10 text-gray-400 rounded-lg hover:text-white hover:bg-white/5 transition-colors">Load
                More Articles</button>
        </div>
    </div>
</x-layouts.app>
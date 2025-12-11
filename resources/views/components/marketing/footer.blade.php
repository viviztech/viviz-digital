<footer class="border-t border-white/5 bg-surface/50">
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div>
                <h4 class="font-display font-bold text-white mb-4">Explore</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="/?type=photos" class="hover:text-white transition-colors">Photos</a></li>
                    <li><a href="/?type=videos" class="hover:text-white transition-colors">Videos</a></li>
                    <li><a href="/?type=templates" class="hover:text-white transition-colors">Templates</a></li>
                    <li><a href="/?type=audio" class="hover:text-white transition-colors">Audio</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-bold text-white mb-4">Creators</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="/become-creator" class="hover:text-white transition-colors">Become a Creator</a>
                    </li>
                    <li><a href="/creator-resources" class="hover:text-white transition-colors">Resources</a></li>
                    <li><a href="/payouts" class="hover:text-white transition-colors">Payouts</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-bold text-white mb-4">Company</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="/about" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="/contact" class="hover:text-white transition-colors">Contact</a></li>
                    <li><a href="/blog" class="hover:text-white transition-colors">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-bold text-white mb-4">Legal</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="/terms" class="hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="/privacy" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="/licenses" class="hover:text-white transition-colors">Licenses</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/5 mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-gray-500 text-sm">© {{ date('Y') }}
                {{ \App\Models\Setting::get('site_name', 'AuraAssets') }}. All rights reserved.
            </p>
            <div class="flex items-center gap-4">
                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                    </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>
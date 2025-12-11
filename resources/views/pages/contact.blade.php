<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl font-display">
                Get in <span
                    class="bg-gradient-to-r from-neon-purple to-neon-pink bg-clip-text text-transparent">Touch</span>
            </h1>
            <p class="mt-4 text-xl text-gray-400">
                Have questions or need support? We're here to help.
            </p>
        </div>

        <div class="bg-surface-elevated rounded-2xl p-8 border border-white/10 shadow-xl">
            <form action="#" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                        <input type="text" name="name" id="name"
                            class="mt-1 block w-full bg-gray-900 border border-gray-700 rounded-md py-3 px-4 text-white focus:ring-neon-purple focus:border-neon-purple"
                            placeholder="Your Name">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                        <input type="email" name="email" id="email"
                            class="mt-1 block w-full bg-gray-900 border border-gray-700 rounded-md py-3 px-4 text-white focus:ring-neon-purple focus:border-neon-purple"
                            placeholder="you@example.com">
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-300">Subject</label>
                    <input type="text" name="subject" id="subject"
                        class="mt-1 block w-full bg-gray-900 border border-gray-700 rounded-md py-3 px-4 text-white focus:ring-neon-purple focus:border-neon-purple"
                        placeholder="How can we help?">
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-300">Message</label>
                    <textarea name="message" id="message" rows="5"
                        class="mt-1 block w-full bg-gray-900 border border-gray-700 rounded-md py-3 px-4 text-white focus:ring-neon-purple focus:border-neon-purple"
                        placeholder="Tell us more..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="button"
                        class="btn-glow inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Send Message
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
                <h3 class="text-white font-bold mb-1">Email Us</h3>
                <p class="text-gray-400">support@auraassets.com</p>
            </div>
            <div>
                <h3 class="text-white font-bold mb-1">Live Chat</h3>
                <p class="text-gray-400">Available Mon-Fri, 9am - 5pm</p>
            </div>
            <div>
                <h3 class="text-white font-bold mb-1">Follow Us</h3>
                <div class="flex justify-center gap-4 mt-2">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Twitter</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Instagram</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
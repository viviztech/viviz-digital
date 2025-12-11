<?php

use Livewire\Volt\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public Product $product;
    public $rating = 5;
    public $comment = '';
    public $editing = false;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function with()
    {
        return [
            'reviews' => $this->product->reviews()->latest()->with('user')->get(),
            'userReview' => Auth::check() ? $this->product->reviews()->where('user_id', Auth::id())->first() : null,
            'canReview' => Auth::check() && Auth::user()->hasVerifiedEmail() && !$this->product->reviews()->where('user_id', Auth::id())->exists(),
            'isUnverified' => Auth::check() && !Auth::user()->hasVerifiedEmail(),
        ];
    }

    public function save()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        if (!Auth::user()->hasVerifiedEmail()) {
            return $this->redirect('/email/verify');
        }

        $this->product->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->comment = '';
        $this->dispatch('review-added'); // Optional: for toast
    }

    public function delete()
    {
        if (!Auth::check())
            return;

        $this->product->reviews()->where('user_id', Auth::id())->delete();
    }
}; ?>

<div class="mt-16 border-t border-white/5 pt-12">
    <h2 class="text-2xl font-display font-bold text-white mb-8">
        Customer Reviews ({{ $reviews->count() }})
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Review Form or Status -->
        <div class="lg:col-span-1">
            @auth
                @if($canReview)
                    <div class="bg-surface-elevated rounded-2xl p-6 border border-white/5">
                        <h3 class="text-lg font-bold text-white mb-4">Write a Review</h3>
                        <form wire:submit="save">
                            <!-- Rating -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-400 mb-2">Rating</label>
                                <div class="flex gap-2">
                                    @foreach(range(1, 5) as $star)
                                        <button type="button" wire:click="$set('rating', {{ $star }})"
                                            class="text-2xl focus:outline-none transition-colors {{ $rating >= $star ? 'text-neon-pink' : 'text-gray-600 hover:text-gray-500' }}">
                                            ★
                                        </button>
                                    @endforeach
                                </div>
                                @error('rating') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Comment -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-400 mb-2">Comment (Optional)</label>
                                <textarea wire:model="comment" rows="4"
                                    class="w-full bg-surface border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-neon-purple/50 resize-none"
                                    placeholder="Share your thoughts..."></textarea>
                                @error('comment') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit"
                                class="w-full py-3 bg-white text-black font-bold rounded-xl hover:bg-neon-pink hover:text-white transition-all shadow-glow">
                                Submit Review
                            </button>
                        </form>
                    </div>
                @elseif($isUnverified)
                    <div class="bg-surface-elevated rounded-2xl p-8 border border-white/5 text-center">
                        <h3 class="text-white font-bold mb-2">Verify email to review</h3>
                        <p class="text-gray-400 text-sm mb-6">Please verify your email address to leave a review.</p>
                        <a href="/email/verify"
                            class="inline-block w-full py-3 bg-white/10 text-white font-bold rounded-xl hover:bg-white/20 transition-all">
                            Verify Email
                        </a>
                    </div>
                @elseif($userReview)
                    <div class="bg-surface-elevated rounded-2xl p-6 border border-white/5 text-center">
                        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">✨</span>
                        </div>
                        <h3 class="text-white font-bold mb-2">Thanks for your review!</h3>
                        <p class="text-gray-400 text-sm mb-4">You rated this product {{ $userReview->rating }} stars.</p>
                        <button wire:click="delete" class="text-red-400 text-sm hover:text-red-300">
                            Delete Review
                        </button>
                    </div>
                @endif
            @else
                <div class="bg-surface-elevated rounded-2xl p-8 border border-white/5 text-center">
                    <h3 class="text-white font-bold mb-2">Sign in to review</h3>
                    <p class="text-gray-400 text-sm mb-6">Share your experience with this product.</p>
                    <a href="/login"
                        class="inline-block w-full py-3 bg-white/10 text-white font-bold rounded-xl hover:bg-white/20 transition-all">
                        Log In
                    </a>
                </div>
            @endauth
        </div>

        <!-- Review List -->
        <div class="lg:col-span-2 space-y-6">
            @forelse($reviews as $review)
                <div class="bg-surface rounded-2xl p-6 border border-white/5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-full bg-gradient-to-br from-neon-purple to-neon-pink flex items-center justify-center text-white font-bold text-sm">
                                {{ substr($review->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-sm">{{ $review->user->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex text-neon-pink">
                            @for($i = 1; $i <= 5; $i++)
                                <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                        <p class="text-gray-300 text-sm leading-relaxed">{{ $review->comment }}</p>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
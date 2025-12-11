<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function sendVerification(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: '/');

            return;
        }

        auth()->user()->sendEmailVerificationNotification();

        session()->flash('status', 'verification-link-sent');
    }

    public function logout(Illuminate\Http\Request $request): void
    {
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="min-h-screen bg-deep-void flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-surface-elevated rounded-2xl border border-white/5 p-8 shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-neon-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-2xl font-display font-bold text-white mb-2">Verify Your Email</h2>
            <p class="text-gray-400">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the
                link we just emailed to you?
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div
                class="mb-6 bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-4 text-sm text-emerald-400 text-center">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="space-y-4">
            <button wire:click="sendVerification"
                class="w-full py-3 bg-gradient-to-r from-neon-purple to-neon-pink text-white font-bold rounded-xl hover:shadow-glow transition-all transform hover:scale-105">
                Resend Verification Email
            </button>

            <button wire:click="logout" type="button"
                class="w-full py-3 bg-surface border border-white/10 text-gray-300 font-medium rounded-xl hover:bg-white/5 hover:text-white transition-colors">
                Log Out
            </button>
        </div>
    </div>
</div>
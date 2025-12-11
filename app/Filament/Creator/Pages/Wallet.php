<?php

namespace App\Filament\Creator\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;

use Livewire\Attributes\On;
use Razorpay\Api\Api;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class Wallet extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'filament.creator.pages.wallet';

    protected static ?string $title = 'My Wallet';

    public $amountToAdd = 100;

    public function initiateTopUp()
    {
        $this->validate([
            'amountToAdd' => 'required|numeric|min:1|max:50000',
        ]);

        $keyId = Setting::get('razorpay_key_id');
        $keySecret = Setting::get('razorpay_key_secret');

        if (!$keyId || !$keySecret) {
            Notification::make()->title('Payment Configuration Missing')->danger()->send();
            return;
        }

        try {
            $api = new Api($keyId, $keySecret);
            $amountInPaise = $this->amountToAdd * 100;

            $razorpayOrder = $api->order->create([
                'receipt' => 'rcptid_' . time() . '_' . Auth::id(),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            $this->dispatch(
                'init-razorpay',
                key: $keyId,
                amount: $amountInPaise,
                order_id: $razorpayOrder['id'],
                name: Setting::get('site_name', 'AuraAssets'),
                description: 'Wallet Top-up',
                prefill: [
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                theme_color: '#6366f1'
            );

        } catch (\Exception $e) {
            Log::error('Razorpay Init Error: ' . $e->getMessage());
            Notification::make()->title('Could not initiate payment')->body($e->getMessage())->danger()->send();
        }
    }

    #[On('payment-success')]
    public function verifyTopUp($response)
    {
        $keyId = Setting::get('razorpay_key_id');
        $keySecret = Setting::get('razorpay_key_secret');

        $api = new Api($keyId, $keySecret);

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $response['razorpay_order_id'],
                'razorpay_payment_id' => $response['razorpay_payment_id'],
                'razorpay_signature' => $response['razorpay_signature']
            ]);

            $payment = $api->payment->fetch($response['razorpay_payment_id']);
            $amountInPaise = $payment->amount;

            Auth::user()->wallet->credit(
                $amountInPaise,
                'Wallet Top-up via Razorpay',
                $response['razorpay_payment_id'],
                'success'
            );

            $this->amountToAdd = 100;
            Notification::make()->title('Funds added successfully')->success()->send();

        } catch (\Exception $e) {
            Log::error('Razorpay Verification Error: ' . $e->getMessage());
            Notification::make()->title('Payment Verification Failed')->body($e->getMessage())->danger()->send();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\WalletTransaction::query()
                    ->whereHas('wallet', function ($query) {
                        $query->where('user_id', Auth::id());
                    })
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn($state) => \App\Models\Setting::get('currency_symbol', '₹') . number_format($state / 100, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                    }),
            ]);
    }

    protected function getViewData(): array
    {
        $user = Auth::user();
        if (!$user->wallet) {
            $user->wallet()->create([
                'balance' => 0,
                'currency' => 'INR'
            ]);
            $user->refresh();
        }

        return [
            'balance' => $user->wallet->balance,
            'currencySymbol' => \App\Models\Setting::get('currency_symbol', '₹'),
        ];
    }
}

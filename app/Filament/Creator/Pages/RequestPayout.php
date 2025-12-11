<?php

namespace App\Filament\Creator\Pages;

use Filament\Pages\Page;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WithdrawalRequest;

class RequestPayout extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-currency-rupee';
    protected static ?string $title = 'Request Payout';

    protected static string $view = 'filament.creator.pages.request-payout';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payout Details')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount (INR)')
                            ->numeric()
                            ->prefix('₹')
                            ->required()
                            ->minValue(100) // Minimum withdrawal
                            ->maxValue(fn() => Auth::user()->wallet->balance / 100) // In rupees
                            ->helperText(fn() => 'Available Balance: ₹' . number_format(Auth::user()->wallet->balance / 100, 2)),

                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'bank_transfer' => 'Bank Transfer',
                                'upi' => 'UPI',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('account_number')
                                    ->requiredIf('payment_method', 'bank_transfer'),
                                Forms\Components\TextInput::make('ifsc_code')
                                    ->label('IFSC Code')
                                    ->requiredIf('payment_method', 'bank_transfer'),
                                Forms\Components\TextInput::make('account_holder_name')
                                    ->requiredIf('payment_method', 'bank_transfer'),
                            ])
                            ->visible(fn(Forms\Get $get) => $get('payment_method') === 'bank_transfer'),

                        Forms\Components\TextInput::make('upi_id')
                            ->label('UPI ID (VPA)')
                            ->requiredIf('payment_method', 'upi')
                            ->visible(fn(Forms\Get $get) => $get('payment_method') === 'upi'),
                    ])
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $amountInPaise = $data['amount'] * 100;

        if ($user->wallet->balance < $amountInPaise) {
            Notification::make()->title('Insufficient Balance')->danger()->send();
            return;
        }

        DB::transaction(function () use ($user, $data, $amountInPaise) {
            // 1. Debit Wallet
            $user->wallet->debit(
                $amountInPaise,
                "Withdrawal Request Created"
            );

            // 2. Create Request
            $paymentDetails = [];
            if ($data['payment_method'] === 'bank_transfer') {
                $paymentDetails = [
                    'account_number' => $data['account_number'],
                    'ifsc_code' => $data['ifsc_code'],
                    'account_holder_name' => $data['account_holder_name'],
                ];
            } else {
                $paymentDetails = ['upi_id' => $data['upi_id']];
            }

            WithdrawalRequest::create([
                'user_id' => $user->id,
                'amount' => $amountInPaise,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_details' => $paymentDetails,
            ]);
        });

        Notification::make()
            ->title('Withdrawal Request Submitted')
            ->success()
            ->send();

        $this->form->fill(); // Reset form
        $this->redirect(Wallet::getUrl()); // Redirect to wallet page
    }
}

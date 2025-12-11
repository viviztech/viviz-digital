<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WithdrawalRequestResource\Pages;
use App\Filament\Admin\Resources\WithdrawalRequestResource\RelationManagers;
use App\Models\WithdrawalRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WithdrawalRequestResource extends Resource
{
    protected static ?string $model = WithdrawalRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->formatStateUsing(fn($state) => $state / 100)
                    ->disabled(),
                Forms\Components\TextInput::make('status')
                    ->disabled(),
                Forms\Components\KeyValue::make('payment_details')
                    ->disabled(),
                Forms\Components\Textarea::make('admin_note'),
                Forms\Components\Textarea::make('rejection_reason'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn($state) => \App\Models\Setting::get('currency_symbol', '₹') . number_format($state / 100, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(WithdrawalRequest $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_note')
                            ->label('Transaction Reference / Note')
                            ->required(),
                    ])
                    ->action(function (WithdrawalRequest $record, array $data) {
                        $record->update([
                            'status' => 'paid',
                            'admin_note' => $data['admin_note'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Marked as Paid')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(WithdrawalRequest $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required(),
                    ])
                    ->action(function (WithdrawalRequest $record, array $data) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                            // 1. Refund Wallet
                            $record->user->wallet->credit(
                                $record->amount,
                                "Refund: Withdrawal Request Rejected",
                                "REF-" . $record->id
                            );

                            // 2. Update Status
                            $record->update([
                                'status' => 'rejected',
                                'rejection_reason' => $data['rejection_reason'],
                            ]);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Request Rejected & Refunded')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawalRequests::route('/'),
        ];
    }
}

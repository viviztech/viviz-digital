<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WalletResource\Pages;
use App\Filament\Admin\Resources\WalletResource\RelationManagers;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Finance';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->description(fn($record) => $record->user->email)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->formatStateUsing(fn($state) => \App\Models\Setting::get('currency_symbol', '₹') . number_format($state / 100, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('adjust_balance')
                    ->label('Adjust Balance')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\Select::make('type')
                            ->options([
                                'credit' => 'Credit (Add Funds)',
                                'debit' => 'Debit (Remove Funds)',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Amount (INR)')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        \Filament\Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (Wallet $record, array $data) {
                        $amountInPaise = $data['amount'] * 100;

                        if ($data['type'] === 'credit') {
                            $record->credit($amountInPaise, $data['description'], null, 'success');
                            \Filament\Notifications\Notification::make()
                                ->title('Funds Credited')
                                ->success()
                                ->send();
                        } else {
                            try {
                                $record->debit($amountInPaise, $data['description'], null);
                                \Filament\Notifications\Notification::make()
                                    ->title('Funds Debited')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Transaction Failed')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWallets::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->disabled(),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled(),

                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'refunded' => 'Refunded',
                                'failed' => 'Failed',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Breakdown')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->prefix(\App\Models\Setting::get('currency_symbol', '$'))
                            ->formatStateUsing(fn($state) => number_format($state / 100, 2))
                            ->disabled(),

                        Forms\Components\TextInput::make('platform_fee')
                            ->prefix(\App\Models\Setting::get('currency_symbol', '$'))
                            ->formatStateUsing(fn($state) => number_format($state / 100, 2))
                            ->disabled(),

                        Forms\Components\TextInput::make('vendor_amount')
                            ->prefix(\App\Models\Setting::get('currency_symbol', '$'))
                            ->formatStateUsing(fn($state) => number_format($state / 100, 2))
                            ->disabled(),

                        Forms\Components\TextInput::make('payment_intent_id')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->copyable()
                    ->limit(12),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('product.shop.name')
                    ->label('Vendor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Total')
                    ->formatStateUsing(fn(int $state): string => \App\Models\Setting::get('currency_symbol', '$') . number_format($state / 100, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('platform_fee')
                    ->label('Fee')
                    ->formatStateUsing(fn(int $state): string => \App\Models\Setting::get('currency_symbol', '$') . number_format($state / 100, 2))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('vendor_amount')
                    ->label('Vendor')
                    ->formatStateUsing(fn(int $state): string => \App\Models\Setting::get('currency_symbol', '$') . number_format($state / 100, 2))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => fn($state) => in_array($state, ['refunded', 'failed']),
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'refunded' => 'Refunded',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\SelectFilter::make('product.shop')
                    ->relationship('product.shop', 'name')
                    ->label('Vendor')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('complete')
                    ->label('Mark Complete')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'completed']);
                        Notification::make()
                            ->title('Order marked as completed')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn(Order $record) => $record->status === 'pending')
                    ->color('success'),
                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'refunded']);
                        Notification::make()
                            ->title('Order refunded')
                            ->warning()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn(Order $record) => $record->status === 'completed')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('complete_all')
                        ->label('Complete Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn($records) => $records->each->update(['status' => 'completed']))
                        ->requiresConfirmation()
                        ->color('success'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

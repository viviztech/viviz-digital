<?php

namespace App\Filament\Creator\Resources;

use App\Filament\Creator\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Products';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        // Only show products for the current user's shop
        return parent::getEloquentQuery()
            ->whereHas('shop', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Details')
                    ->description('Basic information about your digital asset')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state) . '-' . \Illuminate\Support\Str::random(6))),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('categories', 'slug'),
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->maxLength(2000),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->required()
                            ->prefix(\App\Models\Setting::get('currency_symbol', '₹'))
                            ->step(0.01)
                            ->formatStateUsing(fn($state) => $state ? $state / 100 : null)
                            ->dehydrateStateUsing(fn($state) => (int) round($state * 100))
                            ->helperText('Enter amount in INR (e.g., 500 for ₹500.00)')
                            ->minValue(0.99)
                            ->maxValue(999999),
                    ]),

                Forms\Components\Section::make('Files')
                    ->description('Upload your digital asset files')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Asset File')
                            ->required()
                            ->disk('local')
                            ->directory('assets/products')
                            ->visibility('private')
                            ->maxSize(512000) // 500MB max for chunked upload
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'video/mp4',
                                'video/quicktime',
                                'video/webm',
                                'audio/mpeg',
                                'audio/wav',
                                'application/zip',
                                'application/x-rar-compressed',
                            ])
                            ->helperText('Supported: JPG, PNG, WebP, MP4, MOV, WebM, MP3, WAV, ZIP, RAR (max 500MB)'),

                        Forms\Components\FileUpload::make('preview_url')
                            ->label('Preview Image')
                            ->image()
                            ->disk('public')
                            ->directory('previews')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080'),
                    ]),

                Forms\Components\Section::make('AI Metadata')
                    ->description('Auto-generated tags and descriptions')
                    ->collapsed()
                    ->schema([
                        Forms\Components\KeyValue::make('ai_metadata')
                            ->label('Tags & Metadata')
                            ->keyLabel('Property')
                            ->valueLabel('Value')
                            ->addActionLabel('Add Tag')
                            ->reorderable(),
                    ]),

                Forms\Components\Section::make('Visibility')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Make this product visible on the marketplace'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false)
                            ->helperText('Request featuring on the homepage (subject to approval)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('preview_url')
                    ->label('Preview')
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://via.placeholder.com/80'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn(int $state): string => \App\Models\Setting::get('currency_symbol', '₹') . number_format($state / 100, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('downloads_count')
                    ->label('Downloads')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Published'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_stats')
                    ->label('Stats')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn(Product $record): string => route('filament.creator.resources.products.stats', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn($records) => $records->each->update(['is_active' => false]))
                        ->color('danger'),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

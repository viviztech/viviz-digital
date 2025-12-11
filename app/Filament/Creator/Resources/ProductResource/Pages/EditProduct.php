<?php

namespace App\Filament\Creator\Resources\ProductResource\Pages;

use App\Filament\Creator\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('preview')
                ->label('View on Marketplace')
                ->icon('heroicon-o-eye')
                ->url(fn() => route('products.show', $this->record))
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

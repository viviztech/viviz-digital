<?php

namespace App\Filament\Creator\Resources\ProductResource\Pages;

use App\Filament\Creator\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Upload New Asset'),
        ];
    }
}

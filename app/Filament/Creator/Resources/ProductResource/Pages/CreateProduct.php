<?php

namespace App\Filament\Creator\Resources\ProductResource\Pages;

use App\Filament\Creator\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatically assign the product to the current user's shop
        $shop = Auth::user()->shop;

        if (!$shop) {
            // Create a shop for the user if they don't have one
            $shop = Auth::user()->shop()->create([
                'name' => Auth::user()->name . "'s Shop",
                'slug' => \Illuminate\Support\Str::slug(Auth::user()->name) . '-' . \Illuminate\Support\Str::random(4),
            ]);
        }

        $data['shop_id'] = $shop->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

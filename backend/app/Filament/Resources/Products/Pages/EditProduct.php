<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Support\LocalizedUrl;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Sitede önizle')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => app(LocalizedUrl::class)->route('product', config('nuttime.default_locale'), [
                    'slug' => $this->record->translationFor(config('nuttime.default_locale'))?->slug ?: $this->record->slug,
                ]), shouldOpenInNewTab: true),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

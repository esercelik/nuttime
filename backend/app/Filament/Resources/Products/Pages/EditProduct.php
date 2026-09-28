<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
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
                ->url(fn (): string => rtrim(config('app.url'), '/').'/'.match ($this->record->slug) {
                    'antep-fistikli-kremasi' => '#urun-antep-fistigi',
                    'findik-kremasi' => '#urun-findik',
                    'hindistan-cevizi-ezmesi' => '#urun-hindistan-cevizi',
                    'badem-ezmesi' => '#urun-badem',
                    'yer-fistigi-ezmesi' => '#urun-yer-fistigi',
                    default => '#lezzetler',
                }, shouldOpenInNewTab: true),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

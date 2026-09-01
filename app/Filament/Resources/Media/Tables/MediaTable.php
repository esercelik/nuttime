<?php

namespace App\Filament\Resources\Media\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')->label('Ön izleme')->disk('public'),
                TextColumn::make('title')->label('Başlık')->searchable(),
                TextColumn::make('original_name')->label('Dosya')->searchable()->toggleable(),
                TextColumn::make('mime_type')->label('Tip')->badge(),
                TextColumn::make('size')->label('Boyut')->numeric()->suffix(' B')->sortable(),
                TextColumn::make('folder')->label('Klasör')->searchable(),
                TextColumn::make('created_at')->label('Yüklendi')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('mime_type')->label('Dosya tipi')->options(['image/png' => 'PNG', 'image/jpeg' => 'JPEG', 'image/webp' => 'WebP', 'image/avif' => 'AVIF', 'application/pdf' => 'PDF', 'video/mp4' => 'MP4']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->requiresConfirmation()->modalDescription('Kullanımda olan medya kayıtları silinmez; kullanılmayan dosyalar diskten kaldırılır.'),
                ]),
            ]);
    }
}

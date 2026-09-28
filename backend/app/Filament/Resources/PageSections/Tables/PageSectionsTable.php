<?php

namespace App\Filament\Resources\PageSections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PageSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page_key')->label('Sayfa')->badge()->searchable(),
                TextColumn::make('key')->label('Bölüm anahtarı')->searchable()->sortable(),
                TextColumn::make('type')->label('Tip')->badge(),
                TextColumn::make('status')->label('Durum')->badge(),
                ToggleColumn::make('is_active')->label('Aktif'),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
                TextColumn::make('updated_at')->label('Güncellendi')->since()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('page_key')->options(['home' => 'Ana sayfa']),
                SelectFilter::make('status')->options(['draft' => 'Taslak', 'published' => 'Yayında', 'archived' => 'Arşiv']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
    }
}

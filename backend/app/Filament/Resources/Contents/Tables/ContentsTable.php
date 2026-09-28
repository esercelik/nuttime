<?php

namespace App\Filament\Resources\Contents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                TextColumn::make('status')->label('Durum')->badge(),
                TextColumn::make('published_at')->label('Yayın tarihi')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('updated_at')->label('Güncelleme')->since(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['draft' => 'Taslak', 'published' => 'Yayınlandı']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

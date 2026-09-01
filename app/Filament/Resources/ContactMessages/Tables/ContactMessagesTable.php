<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ad soyad')->searchable()->sortable(),
                TextColumn::make('email')->label('E-posta')->searchable(),
                TextColumn::make('phone')->label('Telefon')->toggleable(),
                TextColumn::make('subject')->label('Konu')->toggleable(),
                TextColumn::make('locale')->label('Dil')->badge(),
                IconColumn::make('is_read')->label('Okundu')->boolean(),
                IconColumn::make('is_answered')->label('Cevaplandı')->boolean(),
                IconColumn::make('archived_at')->label('Arşiv')->boolean(),
                TextColumn::make('created_at')->label('Tarih')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('locale')->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch']),
                SelectFilter::make('is_read')->options(['1' => 'Okundu', '0' => 'Okunmadı']),
                SelectFilter::make('is_answered')->options(['1' => 'Cevaplandı', '0' => 'Cevaplanmadı']),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markRead')->label('Okundu işaretle')->visible(fn ($record): bool => ! $record->is_read)->action(fn ($record) => $record->markRead()),
                Action::make('markAnswered')->label('Cevaplandı işaretle')->visible(fn ($record): bool => ! $record->is_answered)->action(fn ($record) => $record->markAnswered()),
                Action::make('archive')->label('Arşivle')->visible(fn ($record): bool => ! $record->archived_at)->action(fn ($record) => $record->archive()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('archive')->label('Seçilenleri arşivle')->action(fn ($records) => $records->each->archive()),
                ]),
            ]);
    }
}

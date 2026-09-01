<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Zaman')->dateTime()->sortable(),
                TextColumn::make('user.name')->label('Kullanıcı')->default('Sistem')->searchable(),
                TextColumn::make('event')->label('İşlem')->badge(),
                TextColumn::make('auditable_type')->label('Kayıt türü')->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('auditable_id')->label('Kayıt')->sortable(),
                TextColumn::make('ip_address')->label('IP')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event')->options(['created' => 'Oluşturuldu', 'updated' => 'Güncellendi', 'deleted' => 'Silindi', 'restored' => 'Geri yüklendi', 'force_deleted' => 'Kalıcı silindi']),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}

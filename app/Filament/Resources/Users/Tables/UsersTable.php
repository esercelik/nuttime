<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Yönetici')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')->label('Rol')->badge(),
                TextColumn::make('last_login_at')->label('Son giriş')->since(),
                TextColumn::make('created_at')->label('Oluşturuldu')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->options(['super_admin' => 'Süper Admin', 'manager' => 'Yönetici', 'editor' => 'İçerik Editörü', 'translator' => 'Çevirmen', 'viewer' => 'Sadece Görüntüleme']),
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

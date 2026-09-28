<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Yönetici hesabı')->schema([
                    TextInput::make('name')->label('Ad soyad')->required()->maxLength(120),
                    TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                    Select::make('role')->label('Rol')->options(['super_admin' => 'Süper Admin', 'manager' => 'Yönetici', 'editor' => 'İçerik Editörü', 'translator' => 'Çevirmen', 'viewer' => 'Sadece Görüntüleme'])->required(),
                    TextInput::make('password')->password()->revealable()->required(fn (string $operation): bool => $operation === 'create')->dehydrateStateUsing(fn (string $state): string => Hash::make($state))->dehydrated(fn ($state): bool => filled($state))->helperText('Boş bırakırsanız mevcut parola korunur.'),
                ])->columns(2),
            ]);
    }
}

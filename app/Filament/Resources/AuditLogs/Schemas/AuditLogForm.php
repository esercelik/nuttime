<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('event')->disabled(),
                TextInput::make('auditable_type')->disabled(),
                TextInput::make('auditable_id')->disabled(),
                Textarea::make('old_values')->disabled()->formatStateUsing(fn ($state): string => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
                Textarea::make('new_values')->disabled()->formatStateUsing(fn ($state): string => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            ]);
    }
}

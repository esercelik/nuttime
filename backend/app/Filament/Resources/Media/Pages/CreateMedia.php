<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Models\Media;
use App\Support\MediaUploadMetadata;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $metadata = app(MediaUploadMetadata::class)->forStoredFile('public', $data['path']);

        if (Media::query()->where('checksum', $metadata['checksum'])->exists()) {
            Storage::disk('public')->delete($data['path']);

            throw ValidationException::withMessages(['path' => 'Bu dosya medya kütüphanesinde zaten mevcut.']);
        }

        $data['original_name'] ??= basename((string) $data['path']);
        $data = [...$data, ...$metadata];

        return $data;
    }
}

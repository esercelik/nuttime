<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $path = Storage::disk('public')->path($data['path']);
        $image = @getimagesize($path);

        $data['original_name'] ??= basename((string) $data['path']);
        $data['mime_type'] = mime_content_type($path) ?: 'application/octet-stream';
        $data['size'] = Storage::disk('public')->size($data['path']);
        $data['width'] = $image[0] ?? null;
        $data['height'] = $image[1] ?? null;
        $data['checksum'] = hash_file('sha256', $path);

        return $data;
    }
}

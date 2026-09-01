<?php

namespace App\Support;

use App\Models\Media;

final class MediaLibraryOptions
{
    /** @return array<string, string> */
    public static function images(): array
    {
        return Media::query()->where('mime_type', 'like', 'image/%')->latest()->limit(200)->get()
            ->mapWithKeys(fn (Media $media): array => [$media->path => $media->title ?: $media->original_name])->all();
    }

    /** @return array<string, string> */
    public static function documents(): array
    {
        return Media::query()->where('mime_type', 'application/pdf')->latest()->limit(200)->get()
            ->mapWithKeys(fn (Media $media): array => [$media->path => $media->title ?: $media->original_name])->all();
    }
}

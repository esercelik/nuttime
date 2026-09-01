<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class MediaUploadMetadata
{
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'application/pdf', 'video/mp4'];

    /** @return array{mime_type: string, size: int, width: ?int, height: ?int, checksum: string} */
    public function forStoredFile(string $disk, string $path): array
    {
        $storage = Storage::disk($disk);
        $absolutePath = $storage->path($path);
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($absolutePath);

        if (! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw ValidationException::withMessages(['path' => 'Bu dosya türüne izin verilmiyor.']);
        }

        $dimensions = str_starts_with($mimeType, 'image/') ? @getimagesize($absolutePath) : false;

        return [
            'mime_type' => $mimeType,
            'size' => $storage->size($path),
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'checksum' => hash_file('sha256', $absolutePath),
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class Media extends Model
{
    protected $fillable = ['disk', 'path', 'original_name', 'mime_type', 'size', 'width', 'height', 'folder', 'title', 'alt_texts', 'checksum'];

    protected $casts = ['alt_texts' => 'array'];

    public function altFor(string $locale): string
    {
        return $this->alt_texts[$locale] ?? $this->alt_texts[config('nuttime.fallback_locale')] ?? $this->alt_texts[config('nuttime.default_locale')] ?? '';
    }

    public function isReferenced(): bool
    {
        $path = $this->path;

        return Product::query()->where('main_image', $path)->orWhere('social_image', $path)->exists()
            || Certificate::query()->where('image', $path)->orWhere('document_file', $path)->exists()
            || SliderItem::withTrashed()->where(fn ($query) => $query->where('background_image', $path)->orWhere('mobile_background_image', $path)->orWhere('product_image', $path)->orWhere('decoration_image', $path)->orWhere('mobile_decoration_image', $path))->exists()
            || PageSection::withTrashed()->where('desktop_image', $path)->orWhere('mobile_image', $path)->exists()
            || Content::query()->where('cover_image', $path)->exists();
    }

    protected static function booted(): void
    {
        self::deleting(function (self $media): void {
            if ($media->isReferenced()) {
                throw ValidationException::withMessages(['media' => 'Bu medya kaydı kullanımda olduğu için silinemez.']);
            }

            Storage::disk($media->disk)->delete($media->path);
        });
    }
}

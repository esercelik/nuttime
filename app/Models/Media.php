<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Media extends Model
{
    protected $fillable = ['disk', 'path', 'original_name', 'mime_type', 'size', 'width', 'height', 'folder', 'title', 'alt_texts', 'checksum'];
    protected $casts = ['alt_texts' => 'array'];

    public function altFor(string $locale): string
    {
        return $this->alt_texts[$locale] ?? $this->alt_texts[config('nuttime.fallback_locale')] ?? $this->alt_texts[config('nuttime.default_locale')] ?? '';
    }
}

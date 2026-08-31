<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['site_name', 'logo', 'phone', 'whatsapp', 'email', 'address', 'instagram', 'facebook', 'youtube', 'footer_description', 'seo_title', 'seo_description'];

    public static function current(): self
    {
        $data = Cache::rememberForever('site_settings.current', fn () => static::firstOrCreate([], ['site_name' => 'Nuttime'])->toArray());

        return (new static)->newFromBuilder($data);
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('site_settings.current'));
        static::deleted(fn () => Cache::forget('site_settings.current'));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['site_name', 'logo', 'phone', 'whatsapp', 'email', 'address', 'instagram', 'facebook', 'youtube', 'footer_description', 'seo_title', 'seo_description', 'factory_name', 'factory_address', 'factory_map_latitude', 'factory_map_longitude', 'factory_google_maps_url', 'factory_map_enabled'];

    protected $casts = ['factory_map_enabled' => 'boolean', 'factory_map_latitude' => 'decimal:7', 'factory_map_longitude' => 'decimal:7'];

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

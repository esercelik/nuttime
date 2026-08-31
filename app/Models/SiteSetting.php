<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['site_name', 'logo', 'phone', 'whatsapp', 'email', 'address', 'instagram', 'facebook', 'youtube', 'footer_description', 'seo_title', 'seo_description'];

    public static function current(): self
    {
        return static::firstOrCreate([], ['site_name' => 'Nuttime']);
    }
}

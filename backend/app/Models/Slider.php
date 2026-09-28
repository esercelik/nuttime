<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Slider extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'name', 'status', 'is_active', 'settings'];

    protected $casts = ['is_active' => 'boolean', 'settings' => 'array'];

    public function items(): HasMany
    {
        return $this->hasMany(SliderItem::class);
    }
}

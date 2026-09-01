<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SliderItemTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'eyebrow', 'title', 'description', 'cta_label', 'cta_url'];

    public function sliderItem(): BelongsTo { return $this->belongsTo(SliderItem::class); }
}

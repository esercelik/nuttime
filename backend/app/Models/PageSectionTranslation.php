<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PageSectionTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'eyebrow', 'title', 'description', 'button_label', 'button_url'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }
}

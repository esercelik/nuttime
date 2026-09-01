<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MenuItemTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['locale', 'label'];
    public function menuItem(): BelongsTo { return $this->belongsTo(MenuItem::class); }
}

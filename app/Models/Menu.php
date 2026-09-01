<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Menu extends Model
{
    protected $fillable = ['key', 'name', 'location', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function items(): HasMany { return $this->hasMany(MenuItem::class); }
}

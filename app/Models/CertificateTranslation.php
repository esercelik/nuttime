<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CertificateTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['locale', 'name', 'description'];
    public function certificate(): BelongsTo { return $this->belongsTo(Certificate::class); }
}

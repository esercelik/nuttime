<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'issuer', 'certificate_number', 'issued_at', 'expires_at', 'image', 'document_url', 'document_file', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean', 'issued_at' => 'date', 'expires_at' => 'date'];

    public function translations(): HasMany
    {
        return $this->hasMany(CertificateTranslation::class);
    }

    public function translationFor(string $locale): ?CertificateTranslation
    {
        $translations = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();

        foreach (array_unique([$locale, config('nuttime.fallback_locale'), config('nuttime.default_locale')]) as $fallbackLocale) {
            $translation = $translations->firstWhere('locale', $fallbackLocale);

            if ($translation) {
                return $translation;
            }
        }

        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

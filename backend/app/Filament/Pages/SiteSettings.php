<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use Livewire\WithFileUploads;

class SiteSettings extends Page
{
    use WithFileUploads;

    protected string $view = 'filament.pages.site-settings';

    protected static ?string $title = 'Site Ayarları';

    protected static ?string $navigationLabel = 'Site Ayarları';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public array $data = [];

    public $logo;

    public $defaultOgImage;

    public static function canAccess(): bool
    {
        return Gate::allows('view', SiteSetting::current());
    }

    public function mount(): void
    {
        Gate::authorize('view', SiteSetting::current());
        $this->data = SiteSetting::current()->toArray();
        $this->data['active_locales'] ??= array_keys(config('nuttime.locales'));
        $this->data['default_locale'] ??= config('nuttime.default_locale');
    }

    public function save(): void
    {
        Gate::authorize('update', SiteSetting::current());
        $this->validate([
            'data.site_name' => ['required', 'string', 'max:120'],
            'data.email' => ['nullable', 'email', 'max:160'],
            'data.contact_recipient' => ['nullable', 'email', 'max:160'],
            'data.phone' => ['nullable', 'string', 'max:40'],
            'data.whatsapp' => ['nullable', 'string', 'max:40'],
            'data.seo_title' => ['nullable', 'string', 'max:160'],
            'data.seo_description' => ['nullable', 'string', 'max:320'],
            'data.legal_name' => ['nullable', 'string', 'max:160'],
            'data.working_hours' => ['nullable', 'string', 'max:500'],
            'data.twitter_handle' => ['nullable', 'string', 'max:80'],
            'data.instagram' => ['nullable', 'url:https', 'max:2048'],
            'data.facebook' => ['nullable', 'url:https', 'max:2048'],
            'data.youtube' => ['nullable', 'url:https', 'max:2048'],
            'data.footer_description' => ['nullable', 'string', 'max:500'],
            'data.active_locales' => ['required', 'array', 'min:1'],
            'data.active_locales.*' => ['in:'.implode(',', array_keys(config('nuttime.locales'))), 'distinct'],
            'data.default_locale' => ['required', 'in:'.implode(',', array_keys(config('nuttime.locales')))],
            'data.design_tokens' => ['nullable', 'array'],
            'data.design_tokens.brand' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'data.design_tokens.background' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'data.design_tokens.surface' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'data.design_tokens.ink' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'data.seo_settings' => ['nullable', 'array'],
            'data.seo_settings.robots' => ['nullable', 'in:index,follow,noindex,nofollow'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'defaultOgImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'data.factory_name' => ['nullable', 'string', 'max:160'],
            'data.factory_address' => ['nullable', 'string', 'max:1000'],
            'data.factory_map_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'data.factory_map_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'data.factory_google_maps_url' => ['nullable', 'url:https', 'max:2048'],
            'data.factory_map_enabled' => ['boolean'],
        ]);
        if (! in_array($this->data['default_locale'], $this->data['active_locales'], true)) {
            $this->addError('data.default_locale', 'Varsayılan dil aktif dillerden biri olmalıdır.');

            return;
        }
        if ($this->logo) {
            $this->data['logo'] = $this->logo->store('site', 'public');
        }
        if ($this->defaultOgImage) {
            $this->data['default_og_image'] = $this->defaultOgImage->store('site', 'public');
        }
        SiteSetting::current()->update($this->data);
        $this->dispatch('saved');
    }
}

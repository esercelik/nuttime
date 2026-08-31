<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
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

    public function mount(): void
    {
        $this->data = SiteSetting::current()->toArray();
    }

    public function save(): void
    {
        $this->validate([
            'data.site_name' => ['required', 'string', 'max:120'],
            'data.email' => ['nullable', 'email', 'max:160'],
            'data.phone' => ['nullable', 'string', 'max:40'],
            'data.whatsapp' => ['nullable', 'string', 'max:40'],
            'data.seo_title' => ['nullable', 'string', 'max:160'],
            'data.seo_description' => ['nullable', 'string', 'max:320'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        if ($this->logo) {
            $this->data['logo'] = $this->logo->store('site', 'public');
        }
        SiteSetting::current()->update($this->data);
        $this->dispatch('saved');
    }
}

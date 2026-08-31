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
        if ($this->logo) {
            $this->data['logo'] = $this->logo->store('site', 'public');
        }
        SiteSetting::current()->update($this->data);
        $this->dispatch('saved');
    }
}

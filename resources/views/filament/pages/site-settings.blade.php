<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        <x-filament::section heading="Genel bilgiler">
            <div class="grid gap-5 md:grid-cols-2">
                @foreach(['site_name'=>'Site adı','phone'=>'Telefon','whatsapp'=>'WhatsApp','email'=>'E-posta','instagram'=>'Instagram','facebook'=>'Facebook','youtube'=>'YouTube','seo_title'=>'SEO başlığı'] as $field => $label)
                    <x-filament::input.wrapper><x-filament::input wire:model="data.{{ $field }}" type="text" placeholder="{{ $label }}" /><x-slot name="suffix">{{ $label }}</x-slot></x-filament::input.wrapper>
                @endforeach
                <x-filament::input.wrapper class="md:col-span-2"><textarea wire:model="data.address" class="fi-input block w-full border-0 bg-transparent px-3 py-2" rows="2" placeholder="Adres"></textarea></x-filament::input.wrapper>
                <x-filament::input.wrapper class="md:col-span-2"><textarea wire:model="data.footer_description" class="fi-input block w-full border-0 bg-transparent px-3 py-2" rows="3" placeholder="Footer açıklaması"></textarea></x-filament::input.wrapper>
                <x-filament::input.wrapper class="md:col-span-2"><textarea wire:model="data.seo_description" class="fi-input block w-full border-0 bg-transparent px-3 py-2" rows="3" placeholder="SEO açıklaması"></textarea></x-filament::input.wrapper>
                <div class="md:col-span-2"><label class="fi-fo-field-wrp-label">Logo</label><input wire:model="logo" type="file" accept="image/png,image/jpeg,image/webp" class="fi-input block w-full" /></div>
            </div>
        </x-filament::section>
        <x-filament::button type="submit">Kaydet</x-filament::button>
    </form>
</x-filament-panels::page>

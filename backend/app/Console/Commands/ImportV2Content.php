<?php

namespace App\Console\Commands;

use App\Models\PageSection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportV2Content extends Command
{
    protected $signature = 'nuttime:import-v2-content';

    protected $description = 'Import existing V2 text without overwriting managed content';

    public function handle(): int
    {
        DB::transaction(function (): void {
            foreach (array_keys(config('nuttime.locales')) as $locale) {
                $path = base_path('../src/i18n/messages/'.$locale.'.json');
                if (! File::exists($path)) {
                    continue;
                }
                $dictionary = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
                foreach ($dictionary as $key => $value) {
                    if (! is_string($value)) {
                        continue;
                    }
                    $section = PageSection::query()->firstOrCreate(['page_key' => 'v2', 'key' => $key], ['type' => 'custom', 'status' => 'published', 'is_active' => true]);
                    $section->translations()->firstOrCreate(['locale' => $locale], ['title' => $key, 'description' => $value]);
                }
            }
        });
        $this->info('V2 text imported. Existing edits preserved.');

        return self::SUCCESS;
    }
}

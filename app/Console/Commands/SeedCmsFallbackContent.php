<?php

namespace App\Console\Commands;

use App\Support\CmsInitialContentSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cms:seed-fallback-content')]
#[Description('Idempotently copy the existing Nuttime public defaults into empty CMS tables.')]
final class SeedCmsFallbackContent extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(CmsInitialContentSeeder $seeder): int
    {
        $result = $seeder->seed();

        $this->info("Menus: {$result['menus']}, sections: {$result['sections']}, slider items: {$result['sliderItems']}");

        return self::SUCCESS;
    }
}

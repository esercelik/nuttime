<?php

namespace App\Console\Commands;

use App\Support\InitialCatalogImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('catalog:import-initial')]
#[Description('Idempotently imports Nuttime’s initial catalog and all configured translations.')]
final class ImportInitialCatalog extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(InitialCatalogImporter $importer): int
    {
        $result = $importer->import();

        $this->info("Products created: {$result['created']}, translations created: {$result['translated']}");

        return self::SUCCESS;
    }
}

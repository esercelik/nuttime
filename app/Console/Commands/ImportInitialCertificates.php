<?php

namespace App\Console\Commands;

use App\Support\InitialCertificateImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('certificates:import-initial')]
#[Description('Imports the bundled Nuttime certificates without overwriting existing records or files.')]
final class ImportInitialCertificates extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(InitialCertificateImporter $importer): int
    {
        $result = $importer->import();

        $this->info("Certificates created: {$result['created']}, translations created: {$result['translated']}, media created: {$result['media']}, files copied: {$result['copied']}");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Support\NuttimeProductMedia;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

#[Signature('nuttime:import-images {--source= : HTTPS archive directory to import from.}')]
#[Description('Idempotently import selected Nuttime product images into local public assets.')]
final class ImportNuttimeImages extends Command
{
    public function handle(NuttimeProductMedia $productMedia): int
    {
        $source = rtrim((string) $this->option('source'), '/');

        if (! $this->isAllowedSource($source)) {
            $this->error('Provide an HTTPS source under nuttime.com.tr.');

            return self::FAILURE;
        }

        $imported = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($productMedia->imports() as $item) {
            $destination = public_path($item['destination']);

            if (File::exists($destination)) {
                $this->line("Skipped existing {$item['destination']}");
                $skipped++;

                continue;
            }

            $response = Http::connectTimeout(5)
                ->timeout(30)
                ->retry([200, 500])
                ->accept('image/*')
                ->get($source.'/'.$item['source']);

            if (! $response->successful() || ! $this->isImage($response->body())) {
                $this->warn("Skipped invalid image {$item['source']}");
                $failed++;

                continue;
            }

            File::ensureDirectoryExists(dirname($destination));
            $temporaryFile = tempnam(dirname($destination), 'nuttime-image-');

            if ($temporaryFile === false) {
                $this->error("Unable to create a temporary file for {$item['destination']}");
                $failed++;

                continue;
            }

            File::put($temporaryFile, $response->body());
            File::move($temporaryFile, $destination);
            $this->info("Imported {$item['destination']}");
            $imported++;
        }

        $this->newLine();
        $this->info("Imported: {$imported}; skipped: {$skipped}; failed: {$failed}.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function isAllowedSource(string $source): bool
    {
        $parts = parse_url($source);

        return ($parts['scheme'] ?? null) === 'https'
            && in_array($parts['host'] ?? null, ['nuttime.com.tr', 'www.nuttime.com.tr'], true)
            && ! isset($parts['query'], $parts['fragment']);
    }

    private function isImage(string $contents): bool
    {
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);

        return in_array($mimeType, ['image/jpeg', 'image/png'], true)
            && getimagesizefromstring($contents) !== false;
    }
}

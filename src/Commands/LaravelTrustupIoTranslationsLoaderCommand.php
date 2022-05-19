<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader\Commands;

use Illuminate\Console\Command;

class LaravelTrustupIoTranslationsLoaderCommand extends Command
{
    public $signature = 'laravel-trustup-io-translations-loader';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

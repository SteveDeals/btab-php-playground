<?php

namespace Btab\Console\Commands;

use Illuminate\Console\Command;
use Btab\Jobs\ImportProductsJob;

class SyncProducts extends Command
{
    protected $signature = 'btab:sync';
    protected $description = 'Synchronize products from BTAB API';

    public function handle()
    {
        dispatch(new ImportProductsJob());
        $this->info('BTAB product sync dispatched.');
    }
}

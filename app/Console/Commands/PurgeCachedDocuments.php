<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class PurgeCachedDocuments extends Command
{
    
    protected $signature = 'documents:purge {--ttl=600}';
    protected $description = 'Delete generated documents older that the specified time';

    public function handle()
    {
        $ttl = (int) $this->option('ttl');
        $dir = public_path('cached_result');
        $deleted = 0;

        foreach(glob("{$dir}/*") as $file) {
            if (is_file($file) && (time() - filemtime($file)) >= $ttl) {
                unlink($file);
                $deleted++;
            }
        }

        $this->info("Purged {$deleted} file(s) older than {$ttl} seconds.");
    }
}

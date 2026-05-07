<?php

namespace App\Console\Commands;

use App\Models\DocumentLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class PurgeCachedDocuments extends Command
{
    
    protected $signature = 'documents:purge {--ttl=300}';
    protected $description = 'Delete generated documents older that the specified time';

    public function handle()
    {
        $ttl = (int) $this->option('ttl');
        $dir = storage_path('app/cached_result');
        $deleted = 0;

        foreach (glob("{$dir}/*") as $file) {
            if (!is_file($file))
                continue;

            if ((time() - filemtime($file)) >= $ttl) {
                $filename = basename($file);

                // Update the log record before deleting
                DocumentLog::where('output_filename', $filename)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => now()]);

                unlink($file);
                $deleted++;
            }
        }

        $this->info("Purged {$deleted} file(s) older than {$ttl} seconds.");
    }
}

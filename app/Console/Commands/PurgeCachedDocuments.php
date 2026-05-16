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
            if ((time() - filemtime($file)) < $ttl)
                continue;

            $filename = basename($file);

            // <- skip orphan PDFs , let them be purged on next cycle   | maybe they will be usefull in the house of hearth
            // once their source is also gone
            if (str_ends_with($filename, '.pdf')) {
                $sourceBase = pathinfo($filename, PATHINFO_FILENAME);
                $hasLivingSource = !empty(glob("{$dir}/{$sourceBase}.{docx,xlsx}", GLOB_BRACE));
                if ($hasLivingSource)
                    continue;
            }

            DocumentLog::where('output_filename', $filename)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);

            unlink($file);
            $deleted++;
        }

        $this->info("Purged {$deleted} file(s) older than {$ttl} seconds.");
    }
}

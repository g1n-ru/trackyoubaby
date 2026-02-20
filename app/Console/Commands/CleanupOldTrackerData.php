<?php

namespace App\Console\Commands;

use App\Models\Click;
use App\Models\MetricaSendLog;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class CleanupOldTrackerData extends Command
{
    protected $signature = 'tracker:cleanup {--chunk=1000 : Number of records to delete per batch}';

    protected $description = 'Remove tracker data older than the configured retention period';

    public function handle(): int
    {
        $days = Config::get('tracker.data_retention_days');
        $cutoff = now()->subDays($days);
        $chunkSize = (int) $this->option('chunk');

        $clicksDeleted = $this->deleteInChunks(Click::class, $cutoff, $chunkSize);
        $this->info('Deleted '.$clicksDeleted.' old clicks (with cascading conversions).');

        $logsDeleted = $this->deleteInChunks(MetricaSendLog::class, $cutoff, $chunkSize);
        $this->info('Deleted '.$logsDeleted.' old metrica send logs.');

        return self::SUCCESS;
    }

    /** @param class-string<Model> $model */
    private function deleteInChunks(string $model, mixed $cutoff, int $chunkSize): int
    {
        $totalDeleted = 0;

        do {
            $ids = $model::where('created_at', '<', $cutoff)
                ->orderBy('id')
                ->limit($chunkSize)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $model::whereIn('id', $ids)->delete();
            $totalDeleted += $ids->count();
        } while (true);

        return $totalDeleted;
    }
}

<?php

namespace App\Jobs;

use App\Models\Click;
use App\Services\YandexMetricaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSessionParamsToMetrica implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [1, 5, 10];

    public function __construct(
        public Click $click,
    )
    {
    }

    public function handle(YandexMetricaService $metricaService): void
    {
        $success = $metricaService->sendSessionParams($this->click);

        if ($success) {
            $this->click->ym_sent_at = now();
            $this->click->save();
        }
    }
}

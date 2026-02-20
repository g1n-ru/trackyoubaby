<?php

namespace App\Jobs;

use App\Models\Conversion;
use App\Services\YandexMetricaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendConversionToMetrica implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [1, 5, 10];

    public function __construct(
        public Conversion $conversion,
    )
    {
    }

    public function handle(YandexMetricaService $metricaService): void
    {
        $click = $this->conversion->click;

        if (!$click || !$click->ym_uid) {
            return;
        }

        $success = $metricaService->sendConversion($this->conversion, $click);

        if ($success) {
            $this->conversion->ym_sent_at = now();
            $this->conversion->save();
        }
    }
}

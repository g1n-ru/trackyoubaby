<?php

namespace Database\Seeders;

use App\Models\Click;
use App\Models\Conversion;
use App\Models\MetricaSendLog;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Random\RandomException;

class TrackerDataSeeder extends Seeder
{
    private const CLICKS_PER_DAY = 100_000;

    private const DAYS = 90;

    private const BATCH_SIZE = 4_000;

    private const CONVERSION_RATE = 0.03;

    private const YM_UID_RATE = 0.65;

    private array $hourlyWeights = [
        0 => 2, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 2,
        6 => 3, 7 => 4, 8 => 5, 9 => 6, 10 => 7, 11 => 8,
        12 => 8, 13 => 7, 14 => 7, 15 => 6, 16 => 6, 17 => 5,
        18 => 5, 19 => 4, 20 => 4, 21 => 3, 22 => 2, 23 => 2,
    ];

    private array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
        'Mozilla/5.0 (iPad; CPU OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0',
        'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15',
    ];

    private array $landingUrls = [
        'https://example.com/product/weight-loss',
        'https://example.com/product/skin-care',
        'https://example.com/product/hair-growth',
        'https://example.com/product/joint-relief',
        'https://example.com/product/eye-health',
    ];

    private array $campaigns = [
        'cmp_weight',
        'cmp_skin',
        'cmp_hair',
        'cmp_joints',
        'cmp_eyes',
    ];

    private array $referrers = [
        'https://yandex.ru/search/?text=купить+средство',
        'https://dzen.ru/news/health',
        'https://mail.ru/',
        'https://vk.com/feed',
        'https://ya.ru/',
        null,
        null,
    ];

    private array $currencies = ['RUB', 'RUB', 'RUB', 'RUB', 'USD', 'KZT'];

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $startDate = now()->subDays(self::DAYS);
        $totalWeight = array_sum($this->hourlyWeights);

        $this->command->info('Seeding '.number_format(self::CLICKS_PER_DAY * self::DAYS).' clicks over '.self::DAYS.' days...');
        $this->command->newLine();

        Schema::disableForeignKeyConstraints();

        for ($day = 0; $day < self::DAYS; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            $this->command->info('Day '.($day + 1).'/'.self::DAYS.': '.$currentDate->format('Y-m-d'));

            $this->seedDay($currentDate, $totalWeight);
        }

        Schema::enableForeignKeyConstraints();

        $this->command->newLine();
        $this->command->info('Done! Total: '.number_format(Click::count()).' clicks, '
            .number_format(Conversion::count()).' conversions, '
            .number_format(MetricaSendLog::count()).' logs.');
    }

    /**
     * @throws RandomException
     */
    private function seedDay(CarbonInterface $date, int $totalWeight): void
    {
        $clicksByHour = array_map(function ($weight) use ($totalWeight) {
            return (int) round(self::CLICKS_PER_DAY * $weight / $totalWeight);
        }, $this->hourlyWeights);

        $clicksByHour[12] += self::CLICKS_PER_DAY - array_sum($clicksByHour);

        foreach ($clicksByHour as $hour => $clickCount) {
            if ($clickCount <= 0) {
                continue;
            }

            $this->seedHour($date, $hour, $clickCount);
        }
    }

    /**
     * @throws RandomException
     */
    private function seedHour(CarbonInterface $date, int $hour, int $clickCount): void
    {
        for ($offset = 0; $offset < $clickCount; $offset += self::BATCH_SIZE) {
            $batchSize = min(self::BATCH_SIZE, $clickCount - $offset);
            $this->seedBatch($date, $hour, $batchSize);
        }
    }

    /**
     * @throws RandomException
     */
    private function seedBatch(CarbonInterface $date, int $hour, int $batchSize): void
    {
        $clicks = [];
        $conversions = [];
        $logs = [];
        $campaignCount = count($this->campaigns) - 1;

        for ($i = 0; $i < $batchSize; $i++) {
            $clickId = Str::uuid()->toString();
            $timestamp = $date->copy()->setTime($hour, random_int(0, 59), random_int(0, 59));
            $hasYmUid = random_int(1, 100) <= (self::YM_UID_RATE * 100);
            $ymUid = $hasYmUid ? (string) random_int(1000000000, 9999999999) : null;
            $campaignIndex = random_int(0, $campaignCount);
            $ymSentAt = $hasYmUid ? $timestamp->copy()->addSeconds(random_int(2, 30)) : null;

            $clicks[] = [
                'click_id' => $clickId,
                'ts_utc' => $timestamp->format('Y-m-d H:i:s.v'),
                'ip' => random_int(1, 223).'.'.random_int(0, 255).'.'.random_int(0, 255).'.'.random_int(1, 254),
                'user_agent' => $this->userAgents[array_rand($this->userAgents)],
                'referer' => $this->referrers[array_rand($this->referrers)],
                'subid' => $this->campaigns[$campaignIndex],
                'subid2' => 'ad_'.random_int(1, 50),
                'subid3' => 'grp_'.random_int(1, 20),
                'subid4' => null,
                'landing_url' => $this->landingUrls[$campaignIndex],
                'raw_query_json' => json_encode([
                    'subid' => $this->campaigns[$campaignIndex],
                    'utm_source' => 'yandex',
                    'utm_medium' => 'cpc',
                ]),
                'ym_uid' => $ymUid,
                'ym_sent_at' => $ymSentAt?->format('Y-m-d H:i:s'),
                'created_at' => $timestamp->format('Y-m-d H:i:s'),
                'updated_at' => $timestamp->format('Y-m-d H:i:s'),
            ];

            if (random_int(1, 1000) <= (self::CONVERSION_RATE * 1000)) {
                $convTimestamp = $timestamp->copy()->addMinutes(random_int(5, 120));
                $revenue = random_int(500, 50000) / 100;
                $convSentAt = $hasYmUid ? $convTimestamp->copy()->addSeconds(random_int(1, 15)) : null;
                $orderId = 'ORD-'.strtoupper(Str::random(8));
                $currency = $this->currencies[array_rand($this->currencies)];

                $conversions[] = [
                    'click_id' => $clickId,
                    'revenue' => $revenue,
                    'currency' => $currency,
                    'order_id' => $orderId,
                    'ym_sent_at' => $convSentAt?->format('Y-m-d H:i:s'),
                    'metadata' => json_encode(['source' => 'postback']),
                    'created_at' => $convTimestamp->format('Y-m-d H:i:s'),
                    'updated_at' => $convTimestamp->format('Y-m-d H:i:s'),
                ];

                if ($hasYmUid) {
                    $success = random_int(1, 100) <= 85;
                    $logs[] = [
                        'click_id' => $clickId,
                        'event_type' => 'conversion',
                        'request_payload' => json_encode(['revenue' => $revenue, 'currency' => $currency, 'order_id' => $orderId]),
                        'response_status' => $success ? 200 : 500,
                        'response_body' => $success ? '{"status":"ok"}' : '{"error":"internal_error"}',
                        'success' => $success,
                        'retry_count' => $success ? 0 : random_int(1, 3),
                        'error_message' => $success ? null : 'Metrica API error',
                        'created_at' => $convSentAt?->format('Y-m-d H:i:s'),
                        'updated_at' => $convSentAt?->format('Y-m-d H:i:s'),
                    ];
                }
            }

            if ($hasYmUid && random_int(1, 100) <= 90) {
                $logTimestamp = $timestamp->copy()->addSeconds(random_int(2, 30));
                $success = random_int(1, 100) <= 80;
                $logs[] = [
                    'click_id' => $clickId,
                    'event_type' => 'session_params',
                    'request_payload' => json_encode(['ym_uid' => $ymUid, 'click_id' => $clickId]),
                    'response_status' => $success ? 200 : (random_int(0, 1) === 0 ? 500 : 429),
                    'response_body' => $success ? '{"status":"ok"}' : '{"error":"rate_limited"}',
                    'success' => $success,
                    'retry_count' => $success ? 0 : random_int(1, 3),
                    'error_message' => $success ? null : 'Request failed',
                    'created_at' => $logTimestamp->format('Y-m-d H:i:s'),
                    'updated_at' => $logTimestamp->format('Y-m-d H:i:s'),
                ];
            }
        }

        Click::insert($clicks);

        if (! empty($conversions)) {
            Conversion::insert($conversions);
        }

        if (! empty($logs)) {
            foreach (array_chunk($logs, self::BATCH_SIZE) as $logBatch) {
                MetricaSendLog::insert($logBatch);
            }
        }
    }
}

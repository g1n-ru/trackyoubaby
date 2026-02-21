<?php

namespace App\Services;

use App\Models\Click;
use App\Models\Conversion;
use Illuminate\Support\Carbon;

class ConversionService
{
    public function recordConversion(
        string $clickId,
        string $target,
        ?float $revenue = null,
        ?string $currency = null,
        ?string $orderId = null,
    ): array {
        $click = Click::where('click_id', $clickId)->firstOrFail();

        $conversion = Conversion::create([
            'click_id' => $clickId,
            'link_id' => $click->link_id,
            'target' => $target,
            'revenue' => $revenue,
            'currency' => $currency,
            'order_id' => $orderId,
        ]);

        return [
            'conversion' => $conversion,
            'click' => $click,
        ];
    }

    public function exportOfflineConversions(string $startDate, string $endDate): string
    {
        $conversions = Conversion::with('click')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->get();

        $csv = "UserId,Target,DateTime,Price,Currency,OrderId\n";

        foreach ($conversions as $conversion) {
            $ymUid = $conversion->click?->ym_uid ?? '';
            $dateTime = $conversion->created_at->format('Y-m-d H:i:s');
            $price = $conversion->revenue ?? '';
            $currency = $conversion->currency ?? 'RUB';
            $orderId = $conversion->order_id ?? '';

            $csv .= implode(',', [
                $ymUid,
                $conversion->target,
                $dateTime,
                $price,
                $currency,
                $orderId,
            ])."\n";
        }

        return $csv;
    }
}

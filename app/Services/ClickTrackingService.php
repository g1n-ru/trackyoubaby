<?php

namespace App\Services;

use App\Models\Click;
use Illuminate\Support\Str;

class ClickTrackingService
{
    public function __construct(private SettingService $settingService) {}

    public function recordClick(
        array $params,
        ?string $ip,
        ?string $userAgent,
        ?string $referer,
        ?int $linkId = null,
        ?string $overrideLandingUrl = null,
    ): Click {
        $clickId = Str::uuid()->toString();

        $landingUrl = $overrideLandingUrl ?? $params['landing'] ?? $this->settingService->getDefaultLandingUrl();

        return Click::create([
            'click_id' => $clickId,
            'link_id' => $linkId,
            'ts_utc' => now(),
            'ip' => $ip,
            'user_agent' => $userAgent,
            'referer' => $referer,
            'subid' => $params['subid'] ?? null,
            'subid2' => $params['subid2'] ?? null,
            'subid3' => $params['subid3'] ?? null,
            'subid4' => $params['subid4'] ?? null,
            'landing_url' => $landingUrl,
            'raw_query_json' => $params,
        ]);
    }

    public function buildRedirectUrl(Click $click): string
    {
        $url = $click->landing_url ?: $this->settingService->getDefaultLandingUrl();

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.'click_id='.$click->click_id;
    }

    public function linkClientId(string $clickId, string $ymUid): Click
    {
        $click = Click::where('click_id', $clickId)->firstOrFail();

        $click->ym_uid = $ymUid;

        $click->save();

        return $click;
    }
}

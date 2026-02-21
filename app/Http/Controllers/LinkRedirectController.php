<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\ClickTrackingService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

class LinkRedirectController extends Controller
{
    public function __invoke(
        Request $request,
        string $slug,
        ClickTrackingService $clickTrackingService,
        SettingService $settingService,
    ): RedirectResponse {
        if (! $settingService->isRedirectEnabled()) {
            abort(404);
        }

        $link = Link::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $click = $clickTrackingService->recordClick(
            $request->query->all(),
            $request->ip(),
            $request->userAgent(),
            $request->header('referer'),
            $link->id,
            $link->landing_url,
        );

        $redirectUrl = $clickTrackingService->buildRedirectUrl($click);

        $cookieMaxAge = Config::get('tracker.cookie.max_age');

        Cookie::queue(
            'click_id',
            $click->click_id,
            $cookieMaxAge,
            '/',
            $request->getHost(),
            true,
            false,
        );

        return redirect()->away($redirectUrl);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ClickTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

class ClickController extends Controller
{
    public function __invoke(Request $request, ClickTrackingService $clickTrackingService): RedirectResponse
    {
        $click = $clickTrackingService->recordClick(
            $request->query->all(),
            $request->ip(),
            $request->userAgent(),
            $request->header('referer'),
        );

        $redirectUrl = $clickTrackingService->buildRedirectUrl($click);

        $cookieMaxAge = Config::get('tracker.cookie.max_age');
        $cookieDomain = Config::get('tracker.cookie.domain');
        $cookieSecure = Config::get('tracker.cookie.secure');

        Cookie::queue(
            'click_id',
            $click->click_id,
            $cookieMaxAge,
            '/',
            $cookieDomain,
            $cookieSecure,
            false,
        );

        return redirect()->away($redirectUrl);
    }
}

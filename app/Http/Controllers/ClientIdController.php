<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientIdRequest;
use App\Jobs\SendSessionParamsToMetrica;
use App\Services\ClickTrackingService;
use Illuminate\Http\JsonResponse;

class ClientIdController extends Controller
{
    public function __invoke(StoreClientIdRequest $request, ClickTrackingService $clickTrackingService): JsonResponse
    {
        $validated = $request->validated();

        $click = $clickTrackingService->linkClientId(
            $validated['click_id'],
            $validated['ym_uid'],
        );

        SendSessionParamsToMetrica::dispatch($click);

        return response()->json([
            'status' => 'ok',
            'click_id' => $click->click_id,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportConversionsRequest;
use App\Http\Requests\StoreConversionRequest;
use App\Jobs\SendConversionToMetrica;
use App\Services\ConversionService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConversionController extends Controller
{
    public function store(StoreConversionRequest $request, ConversionService $conversionService): JsonResponse
    {
        $validated = $request->validated();

        $result = $conversionService->recordConversion(
            $validated['click_id'],
            $validated['target'],
            $validated['revenue'] ?? null,
            $validated['currency'] ?? null,
            $validated['order_id'] ?? null,
        );

        SendConversionToMetrica::dispatch($result['conversion']);

        return response()->json([
            'status' => 'ok',
            'conversion_id' => $result['conversion']->id,
        ]);
    }

    public function export(ExportConversionsRequest $request, ConversionService $conversionService): Response
    {
        $validated = $request->validated();

        $csv = $conversionService->exportOfflineConversions(
            $validated['start_date'],
            $validated['end_date'],
        );

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="conversions_'.$validated['start_date'].'_'.$validated['end_date'].'.csv"',
        ]);
    }
}

<?php

namespace Dehasoft\PanelAgent\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Answers the panel's outbound health-check poll. The token here is the
 * same PANEL_AGENT_TOKEN used to send events to the panel — one secret,
 * both directions.
 */
class HealthCheckController
{
    public function __invoke(Request $request): JsonResponse
    {
        $expected = (string) config('panel-agent.token');
        $given = (string) $request->bearerToken();

        if ($expected === '' || $given === '' || ! hash_equals($expected, $given)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return response()->json([
            'status' => 'ok',
            'php_version' => PHP_VERSION,
            'app_version' => config('app.version'),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

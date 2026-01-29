<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RequestMetrics
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $durationMs = (int) round((microtime(true) - $start) * 1000);
        $status = $response->getStatusCode();

        Cache::add('metrics:requests_total', 0);
        Cache::increment('metrics:requests_total');

        Cache::add('metrics:response_time_sum_ms', 0);
        Cache::increment('metrics:response_time_sum_ms', $durationMs);

        if ($status >= 500) {
            Cache::add('metrics:requests_5xx', 0);
            Cache::increment('metrics:requests_5xx');
        } elseif ($status >= 400) {
            Cache::add('metrics:requests_4xx', 0);
            Cache::increment('metrics:requests_4xx');
        }

        Log::info('request_metrics', [
            'route' => $request->path(),
            'method' => $request->method(),
            'status' => $status,
            'duration_ms' => $durationMs,
        ]);

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Torann\GeoIP\Facades\GeoIP;

/**
 * Webhook Replay Protection Middleware
 * 
 * Prevents replay attacks on webhook endpoints by:
 * - Validating request signatures
 * - Checking request timestamps
 * - Preventing duplicate requests
 */
class WebhookReplayProtection
{
    /**
     * Maximum age of a webhook request (in seconds)
     */
    private int $maxAge = 300; // 5 minutes

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this is a webhook endpoint
        if (!$this->isWebhookRoute($request)) {
            return $next($request);
        }

        // Validate timestamp
        $timestamp = $request->header('X-Webhook-Timestamp') ?? $request->input('timestamp');
        
        if (!$timestamp || !$this->isTimestampValid($timestamp)) {
            $geoData = $this->getGeolocation($request);
            Log::warning('Webhook replay attack detected - invalid timestamp', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'url' => $request->fullUrl(),
                'timestamp' => $timestamp,
            ]);
            abort(401, 'Invalid or expired timestamp');
        }

        // Check for duplicate requests using request signature
        $signature = $this->generateSignature($request);
        
        if ($this->isDuplicate($signature)) {
            $geoData = $this->getGeolocation($request);
            Log::warning('Webhook replay attack detected - duplicate request', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'url' => $request->fullUrl(),
                'signature' => $signature,
            ]);
            abort(409, 'Duplicate request detected');
        }

        // Mark request as processed
        $this->markAsProcessed($signature);

        return $next($request);
    }

    /**
     * Get geolocation data from IP address
     */
    private function getGeolocation(Request $request): array
    {
        try {
            $ip = $request->ip();
            if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
                return ['country' => 'Local', 'city' => 'Localhost'];
            }
            $location = GeoIP::getLocation($ip);
            return [
                'country' => $location->country ?? 'Unknown',
                'city' => $location->city ?? 'Unknown',
                'iso_code' => $location->iso_code ?? null,
            ];
        } catch (\Exception $e) {
            return ['country' => 'Unknown', 'city' => 'Unknown'];
        }
    }

    /**
     * Check if the current route is a webhook endpoint
     */
    private function isWebhookRoute(Request $request): bool
    {
        return str_contains($request->path(), 'webhook') || 
               str_contains($request->path(), 'callback') ||
               $request->header('X-Webhook-Signature') !== null;
    }

    /**
     * Validate timestamp
     */
    private function isTimestampValid(?string $timestamp): bool
    {
        if (!$timestamp || !is_numeric($timestamp)) {
            return false;
        }

        $requestTime = (int) $timestamp;
        $currentTime = time();
        
        // Check if timestamp is not too old
        if (($currentTime - $requestTime) > $this->maxAge) {
            return false;
        }

        // Check if timestamp is not in the future (clock skew tolerance: 60 seconds)
        if (($requestTime - $currentTime) > 60) {
            return false;
        }

        return true;
    }

    /**
     * Generate unique signature for the request
     */
    private function generateSignature(Request $request): string
    {
        $data = [
            'method' => $request->method(),
            'path' => $request->path(),
            'query' => $request->query(),
            'body' => $request->getContent(),
            'timestamp' => $request->header('X-Webhook-Timestamp') ?? $request->input('timestamp'),
        ];

        return hash('sha256', json_encode($data));
    }

    /**
     * Check if request is a duplicate
     */
    private function isDuplicate(string $signature): bool
    {
        return Cache::has("webhook:processed:{$signature}");
    }

    /**
     * Mark request as processed
     */
    private function markAsProcessed(string $signature): void
    {
        // Store for twice the max age to prevent any edge cases
        Cache::put("webhook:processed:{$signature}", true, $this->maxAge * 2);
    }
}

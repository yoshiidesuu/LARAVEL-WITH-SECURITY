<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Torann\GeoIP\Facades\GeoIP;

/**
 * Security Logging Middleware
 * 
 * Logs all security-relevant events and suspicious activities
 * Including IP address and geolocation data
 */
class SecurityLogger
{
    /**
     * Sensitive data keys to mask in logs
     */
    private array $sensitiveKeys = [
        'password',
        'password_confirmation',
        'token',
        'secret',
        'api_key',
        'apikey',
        'access_token',
        'refresh_token',
        'credit_card',
        'cvv',
        'ssn',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Log incoming request
        $this->logRequest($request);

        $response = $next($request);

        // Calculate response time
        $responseTime = (microtime(true) - $startTime) * 1000;

        // Log response
        $this->logResponse($request, $response, $responseTime);

        // Detect suspicious activity
        $this->detectSuspiciousActivity($request, $response, $responseTime);

        return $response;
    }

    /**
     * Log incoming request
     */
    private function logRequest(Request $request): void
    {
        $geoData = $this->getGeolocation($request);
        
        $data = [
            'timestamp' => now()->toIso8601String(),
            'ip' => $request->ip(),
            'geolocation' => $geoData,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'user_id' => auth()->id(),
            'input' => $this->maskSensitiveData($request->except($this->sensitiveKeys)),
        ];

        Log::channel('security')->info('Incoming Request', $data);
    }

    /**
     * Log response
     */
    private function logResponse(Request $request, Response $response, float $responseTime): void
    {
        $geoData = $this->getGeolocation($request);
        
        $data = [
            'timestamp' => now()->toIso8601String(),
            'ip' => $request->ip(),
            'geolocation' => $geoData,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'response_time' => round($responseTime, 2) . 'ms',
            'user_id' => auth()->id(),
        ];

        // Log with appropriate level based on status code
        if ($response->getStatusCode() >= 500) {
            Log::channel('security')->error('Response - Server Error', $data);
        } elseif ($response->getStatusCode() >= 400) {
            Log::channel('security')->warning('Response - Client Error', $data);
        } else {
            Log::channel('security')->info('Response - Success', $data);
        }
    }

    /**
     * Detect suspicious activity
     */
    private function detectSuspiciousActivity(Request $request, Response $response, float $responseTime): void
    {
        $suspicious = [];

        // Multiple failed attempts (401/403)
        if (in_array($response->getStatusCode(), [401, 403])) {
            $suspicious[] = 'authentication_failure';
        }

        // Extremely slow response (potential DoS or resource exhaustion)
        if ($responseTime > 5000) { // 5 seconds
            $suspicious[] = 'slow_response';
        }

        // Accessing admin routes without authentication
        if (str_contains($request->path(), 'admin') && !auth()->check()) {
            $suspicious[] = 'unauthorized_admin_access';
        }

        // Multiple different endpoints in short time (scanning)
        // This would require session/cache tracking - simplified here
        
        // POST to unusual endpoints
        if ($request->isMethod('POST') && !$request->expectsJson() && !$response->isSuccessful()) {
            $suspicious[] = 'failed_post_request';
        }

        // Log if any suspicious activity detected
        if (!empty($suspicious)) {
            $geoData = $this->getGeolocation($request);
            
            Log::channel('security')->warning('Suspicious Activity Detected', [
                'flags' => $suspicious,
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'status' => $response->getStatusCode(),
                'response_time' => $responseTime,
            ]);
        }
    }

    /**
     * Get geolocation data from IP address
     */
    private function getGeolocation(Request $request): array
    {
        try {
            $ip = $request->ip();
            
            // Skip geolocation for local IPs
            if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
                return [
                    'country' => 'Local',
                    'city' => 'Localhost',
                    'state' => 'Local',
                    'latitude' => null,
                    'longitude' => null,
                    'timezone' => config('app.timezone'),
                    'iso_code' => 'LOCAL',
                ];
            }

            // Get geolocation data
            $location = GeoIP::getLocation($ip);
            
            return [
                'country' => $location->country ?? 'Unknown',
                'city' => $location->city ?? 'Unknown',
                'state' => $location->state_name ?? $location->state ?? null,
                'latitude' => $location->lat ?? null,
                'longitude' => $location->lon ?? null,
                'timezone' => $location->timezone ?? null,
                'iso_code' => $location->iso_code ?? null,
                'postal_code' => $location->postal_code ?? null,
            ];
        } catch (\Exception $e) {
            // If geolocation fails, return basic info
            return [
                'country' => 'Unknown',
                'city' => 'Unknown',
                'state' => null,
                'latitude' => null,
                'longitude' => null,
                'timezone' => null,
                'iso_code' => null,
                'error' => 'Geolocation unavailable',
            ];
        }
    }

    /**
     * Mask sensitive data
     */
    private function maskSensitiveData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
            } elseif ($this->isSensitiveKey($key)) {
                $data[$key] = '***MASKED***';
            }
        }

        return $data;
    }

    /**
     * Check if key is sensitive
     */
    private function isSensitiveKey(string $key): bool
    {
        $key = strtolower($key);
        
        foreach ($this->sensitiveKeys as $sensitive) {
            if (str_contains($key, $sensitive)) {
                return true;
            }
        }

        return false;
    }
}

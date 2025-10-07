<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Torann\GeoIP\Facades\GeoIP;

/**
 * XSS Protection Middleware
 * 
 * Detects and sanitizes potential XSS attacks in request data
 */
class AntiXss
{
    /**
     * XSS patterns to detect
     */
    private array $xssPatterns = [
        // Script tags
        '/<script[^>]*>.*?<\/script>/is',
        
        // Event handlers
        '/on\w+\s*=\s*["\']?[^"\'>\s]+["\']?/i',
        
        // JavaScript protocol
        '/javascript:/i',
        
        // Data protocol
        '/data:text\/html/i',
        
        // VBScript
        '/vbscript:/i',
        
        // Iframe injection
        '/<iframe[^>]*>/i',
        
        // Object/embed tags
        '/<(object|embed)[^>]*>/i',
        
        // Base64 encoded scripts
        '/base64,.*script/i',
        
        // Expression (IE specific)
        '/expression\s*\(/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allInput = $request->all();
        
        if ($this->containsXss($allInput)) {
            $geoData = $this->getGeolocation($request);
            
            Log::warning('XSS attempt detected', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clean the input instead of blocking (optional: you can abort instead)
            $cleanedInput = $this->sanitizeInput($allInput);
            $request->merge($cleanedInput);
        }

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
     * Check if data contains XSS patterns
     */
    private function containsXss(array $data): bool
    {
        foreach ($data as $value) {
            if (is_array($value)) {
                if ($this->containsXss($value)) {
                    return true;
                }
            } elseif (is_string($value)) {
                foreach ($this->xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Sanitize input data
     */
    private function sanitizeInput(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                $data[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return $data;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Web Application Firewall (WAF) Middleware
 * 
 * Comprehensive protection against various attack vectors
 */
class WebApplicationFirewall
{
    /**
     * Malicious patterns to detect
     */
    private array $maliciousPatterns = [
        // Path traversal
        '/\.\.[\/\\\\]/i',
        '/\%2e\%2e[\/\\\\]/i',
        
        // Command injection
        '/[;&|`$(){}[\]<>]/i',
        
        // Null byte injection
        '/\%00/i',
        
        // LDAP injection
        '/[()!&|]/i',
        
        // XXE (XML External Entity)
        '/<!ENTITY/i',
        
        // SSRF patterns
        '/(localhost|127\.0\.0\.1|::1|169\.254)/i',
        
        // File inclusion
        '/(php|file|data|expect|zip):\/\//i',
    ];

    /**
     * Blocked user agents (bots, scanners)
     */
    private array $blockedUserAgents = [
        'sqlmap',
        'nikto',
        'nmap',
        'masscan',
        'zgrab',
        'acunetix',
        'nessus',
        'openvas',
        'qualys',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limiting
        $this->checkRateLimit($request);

        // Check user agent
        if ($this->isBlockedUserAgent($request)) {
            $this->logAttack($request, 'Blocked User Agent');
            abort(403, 'Access Denied');
        }

        // Check for malicious patterns in URL
        if ($this->containsMaliciousPattern($request->fullUrl())) {
            $this->logAttack($request, 'Malicious URL Pattern');
            abort(403, 'Forbidden Request');
        }

        // Check for malicious patterns in input
        if ($this->containsMaliciousPattern(json_encode($request->all()))) {
            $this->logAttack($request, 'Malicious Input Pattern');
            abort(403, 'Forbidden Request');
        }

        // Check for directory traversal
        if ($this->hasDirectoryTraversal($request)) {
            $this->logAttack($request, 'Directory Traversal Attempt');
            abort(403, 'Access Denied');
        }

        // Check request size (prevent DoS)
        if ($this->isRequestTooLarge($request)) {
            $this->logAttack($request, 'Request Too Large');
            abort(413, 'Payload Too Large');
        }

        return $next($request);
    }

    /**
     * Check rate limiting
     */
    private function checkRateLimit(Request $request): void
    {
        $key = 'waf:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 100)) {
            $this->logAttack($request, 'Rate Limit Exceeded');
            abort(429, 'Too Many Requests');
        }

        RateLimiter::hit($key, 60); // 100 requests per minute
    }

    /**
     * Check if user agent is blocked
     */
    private function isBlockedUserAgent(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        foreach ($this->blockedUserAgents as $blocked) {
            if (str_contains($userAgent, $blocked)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for malicious patterns
     */
    private function containsMaliciousPattern(string $input): bool
    {
        foreach ($this->maliciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for directory traversal
     */
    private function hasDirectoryTraversal(Request $request): bool
    {
        $path = $request->path();
        return str_contains($path, '..') || str_contains($path, '%2e%2e');
    }

    /**
     * Check if request is too large
     */
    private function isRequestTooLarge(Request $request): bool
    {
        $content = $request->getContent();
        return strlen($content) > 10485760; // 10MB limit
    }

    /**
     * Log attack attempt
     */
    private function logAttack(Request $request, string $type): void
    {
        Log::critical("WAF: {$type}", [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'input' => $request->all(),
            'headers' => $request->headers->all(),
        ]);
    }
}

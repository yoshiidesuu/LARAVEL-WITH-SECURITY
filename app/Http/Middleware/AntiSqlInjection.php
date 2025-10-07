<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * SQL Injection Prevention Middleware
 * 
 * Detects and blocks common SQL injection patterns in request data
 */
class AntiSqlInjection
{
    /**
     * SQL injection patterns to detect
     */
    private array $sqlPatterns = [
        // Union-based injection
        '/(\b(union|select|insert|update|delete|drop|create|alter|exec|execute|script|javascript|eval)\b)/i',
        
        // Comment-based injection
        '/(--|\#|\/\*|\*\/)/i',
        
        // Time-based blind injection
        '/(waitfor\s+delay|sleep\(|benchmark\()/i',
        
        // Boolean-based blind injection
        '/(\'\s*(or|and)\s*\'?\d+\'?\s*=\s*\'?\d+)/i',
        
        // Stacked queries
        '/;\s*(drop|delete|update|insert)/i',
        
        // Hex encoding
        '/(0x[0-9a-f]+)/i',
        
        // Information schema
        '/(information_schema|sysobjects|syscolumns)/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check all input data
        $allInput = $request->all();
        
        if ($this->containsSqlInjection($allInput)) {
            Log::critical('SQL Injection attempt detected', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $allInput,
                'user_agent' => $request->userAgent(),
            ]);

            abort(403, 'Forbidden - Malicious request detected');
        }

        return $next($request);
    }

    /**
     * Check if data contains SQL injection patterns
     */
    private function containsSqlInjection(array $data): bool
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->containsSqlInjection($value)) {
                    return true;
                }
            } elseif (is_string($value) || is_string($key)) {
                foreach ($this->sqlPatterns as $pattern) {
                    if (preg_match($pattern, $value) || preg_match($pattern, $key)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

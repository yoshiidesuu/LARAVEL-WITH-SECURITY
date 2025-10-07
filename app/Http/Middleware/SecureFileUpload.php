<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * Secure File Upload Middleware
 * 
 * Validates and secures file uploads to prevent malicious file uploads
 */
class SecureFileUpload
{
    /**
     * Allowed MIME types
     */
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'text/csv',
    ];

    /**
     * Blocked extensions
     */
    private array $blockedExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
        'exe', 'com', 'bat', 'cmd', 'sh', 'bash',
        'js', 'jar', 'py', 'pl', 'rb',
        'asp', 'aspx', 'jsp', 'jspx',
        'dll', 'so', 'dylib',
        'svg', // Can contain XSS
    ];

    /**
     * Maximum file size (in bytes)
     */
    private int $maxFileSize = 10485760; // 10MB

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('file') || count($request->allFiles()) > 0) {
            $files = $request->allFiles();
            
            foreach ($files as $file) {
                if (is_array($file)) {
                    foreach ($file as $singleFile) {
                        $this->validateFile($singleFile, $request);
                    }
                } else {
                    $this->validateFile($file, $request);
                }
            }
        }

        return $next($request);
    }

    /**
     * Validate uploaded file
     */
    private function validateFile($file, Request $request): void
    {
        // Check if file is valid
        if (!$file->isValid()) {
            Log::warning('Invalid file upload attempt', [
                'ip' => $request->ip(),
                'error' => $file->getErrorMessage(),
            ]);
            abort(422, 'Invalid file upload');
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            Log::warning('File too large', [
                'ip' => $request->ip(),
                'size' => $file->getSize(),
            ]);
            abort(422, 'File size exceeds maximum allowed size');
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $this->blockedExtensions)) {
            Log::critical('Blocked file extension upload attempt', [
                'ip' => $request->ip(),
                'extension' => $extension,
                'filename' => $file->getClientOriginalName(),
            ]);
            abort(422, 'File type not allowed');
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            Log::warning('Invalid MIME type upload attempt', [
                'ip' => $request->ip(),
                'mime_type' => $mimeType,
                'filename' => $file->getClientOriginalName(),
            ]);
            abort(422, 'File type not allowed');
        }

        // Check for double extensions
        $filename = $file->getClientOriginalName();
        if (substr_count($filename, '.') > 1) {
            Log::warning('Double extension detected', [
                'ip' => $request->ip(),
                'filename' => $filename,
            ]);
            abort(422, 'Invalid filename');
        }

        // Scan file content for malicious patterns
        if ($this->containsMaliciousContent($file)) {
            Log::critical('Malicious file content detected', [
                'ip' => $request->ip(),
                'filename' => $file->getClientOriginalName(),
            ]);
            abort(422, 'File contains malicious content');
        }
    }

    /**
     * Check file content for malicious patterns
     */
    private function containsMaliciousContent($file): bool
    {
        $content = file_get_contents($file->getRealPath());
        
        $maliciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/eval\(/i',
            '/base64_decode/i',
            '/exec\(/i',
            '/system\(/i',
            '/passthru\(/i',
            '/shell_exec/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }
}

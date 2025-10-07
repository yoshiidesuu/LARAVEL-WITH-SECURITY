<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Torann\GeoIP\Facades\GeoIP;

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
            $geoData = $this->getGeolocation($request);
            Log::warning('Invalid file upload attempt', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'error' => $file->getErrorMessage(),
            ]);
            abort(422, 'Invalid file upload');
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            $geoData = $this->getGeolocation($request);
            Log::warning('File too large', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'size' => $file->getSize(),
            ]);
            abort(422, 'File size exceeds maximum allowed size');
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $this->blockedExtensions)) {
            $geoData = $this->getGeolocation($request);
            Log::critical('Blocked file extension upload attempt', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'extension' => $extension,
                'filename' => $file->getClientOriginalName(),
            ]);
            abort(422, 'File type not allowed');
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            $geoData = $this->getGeolocation($request);
            Log::warning('Invalid MIME type upload attempt', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'mime_type' => $mimeType,
                'filename' => $file->getClientOriginalName(),
            ]);
            abort(422, 'File type not allowed');
        }

        // Check for double extensions
        $filename = $file->getClientOriginalName();
        if (substr_count($filename, '.') > 1) {
            $geoData = $this->getGeolocation($request);
            Log::warning('Double extension detected', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'filename' => $filename,
            ]);
            abort(422, 'Invalid filename');
        }

        // Scan file content for malicious patterns
        if ($this->containsMaliciousContent($file)) {
            $geoData = $this->getGeolocation($request);
            Log::critical('Malicious file content detected', [
                'ip' => $request->ip(),
                'geolocation' => $geoData,
                'filename' => $file->getClientOriginalName(),
            ]);
            abort(422, 'File contains malicious content');
        }
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

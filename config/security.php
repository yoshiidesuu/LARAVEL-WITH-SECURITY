<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable various security features
    |
    */

    'enabled' => env('SECURITY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Web Application Firewall (WAF)
    |--------------------------------------------------------------------------
    */

    'waf' => [
        'enabled' => env('WAF_ENABLED', true),
        
        'rate_limiting' => [
            'enabled' => true,
            'requests' => env('RATE_LIMIT_REQUESTS', 100),
            'duration' => env('RATE_LIMIT_DURATION', 60), // seconds
        ],

        'blocked_user_agents' => [
            'sqlmap',
            'nikto',
            'nmap',
            'masscan',
            'zgrab',
            'acunetix',
            'nessus',
            'openvas',
            'qualys',
        ],

        'max_request_size' => 10485760, // 10MB
    ],

    /*
    |--------------------------------------------------------------------------
    | SQL Injection Protection
    |--------------------------------------------------------------------------
    */

    'sql_injection' => [
        'enabled' => true,
        'block_mode' => true, // Set to false to log only
    ],

    /*
    |--------------------------------------------------------------------------
    | XSS Protection
    |--------------------------------------------------------------------------
    */

    'xss' => [
        'enabled' => true,
        'sanitize_mode' => true, // Set to false to block instead of sanitize
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */

    'headers' => [
        'enabled' => env('SECURITY_HEADERS_ENABLED', true),
        
        'x_frame_options' => 'SAMEORIGIN', // DENY, SAMEORIGIN, ALLOW-FROM uri
        'x_content_type_options' => 'nosniff',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        
        'hsts' => [
            'enabled' => true,
            'max_age' => 31536000, // 1 year
            'include_subdomains' => true,
            'preload' => true,
        ],
        
        'expect_ct' => [
            'enabled' => true,
            'max_age' => 86400, // 24 hours
            'enforce' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */

    'file_upload' => [
        'enabled' => true,
        
        'max_size' => env('MAX_UPLOAD_SIZE', 10485760), // 10MB
        
        'allowed_mime_types' => [
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
        ],
        
        'blocked_extensions' => [
            'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
            'exe', 'com', 'bat', 'cmd', 'sh', 'bash',
            'js', 'jar', 'py', 'pl', 'rb',
            'asp', 'aspx', 'jsp', 'jspx',
            'dll', 'so', 'dylib',
            'svg',
        ],
        
        'scan_content' => true, // Scan file content for malicious patterns
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Protection
    |--------------------------------------------------------------------------
    */

    'webhook' => [
        'enabled' => true,
        'max_age' => env('WEBHOOK_MAX_AGE', 300), // 5 minutes
        'require_signature' => false, // Set to true to require webhook signatures
    ],

    /*
    |--------------------------------------------------------------------------
    | CSRF Protection
    |--------------------------------------------------------------------------
    */

    'csrf' => [
        'enabled' => true,
        'token_lifetime' => env('CSRF_TOKEN_LIFETIME', 7200), // 2 hours
        
        'excluded_routes' => [
            'webhook/*',
            'api/*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */

    'session' => [
        'encrypt' => env('SESSION_ENCRYPT', true),
        'http_only' => env('SESSION_HTTP_ONLY', true),
        'same_site' => env('SESSION_SAME_SITE', 'strict'),
        'secure' => env('SESSION_SECURE_COOKIE', false), // Set to true in production with HTTPS
        'lifetime' => env('SESSION_LIFETIME', 120),
        'regenerate_on_login' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Logging
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'enabled' => true,
        'channel' => 'security',
        
        'log_requests' => true,
        'log_responses' => true,
        'log_errors' => true,
        
        'mask_sensitive_data' => true,
        'sensitive_keys' => [
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
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist/Blacklist
    |--------------------------------------------------------------------------
    */

    'ip_filtering' => [
        'enabled' => false,
        
        'whitelist' => [
            // '127.0.0.1',
        ],
        
        'blacklist' => [
            // Add known malicious IPs
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    */

    '2fa' => [
        'enabled' => false, // Enable when ready to use
        'issuer' => env('APP_NAME', 'Laravel'),
    ],

];

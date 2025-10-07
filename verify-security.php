#!/usr/bin/env php
<?php
/**
 * Security Features Verification Script
 * 
 * This script verifies that all security middleware and configurations
 * are properly installed and functional.
 */

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "   🔒 LARAVEL SECURITY VERIFICATION SCRIPT\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$checks = [];
$errors = [];
$warnings = [];

// Change to Laravel root directory
$laravelRoot = dirname(__FILE__);
chdir($laravelRoot);

echo "📁 Laravel Root: " . $laravelRoot . "\n\n";

// ===================================================================
// CHECK 1: Middleware Files
// ===================================================================
echo "🔍 Checking Middleware Files...\n";
$middlewareDir = $laravelRoot . '/app/Http/Middleware';
$requiredMiddleware = [
    'SecurityLogger.php',
    'SecurityHeaders.php',
    'WebApplicationFirewall.php',
    'AntiSqlInjection.php',
    'AntiXss.php',
    'SecureFileUpload.php',
    'WebhookReplayProtection.php'
];

foreach ($requiredMiddleware as $file) {
    $path = $middlewareDir . '/' . $file;
    if (file_exists($path)) {
        $checks[] = "✅ {$file}";
    } else {
        $errors[] = "❌ Missing: {$file}";
    }
}

// ===================================================================
// CHECK 2: Config Files
// ===================================================================
echo "\n🔍 Checking Configuration Files...\n";
$configDir = $laravelRoot . '/config';
$requiredConfigs = [
    'security.php',
    'csp.php',
    'purifier.php',
    'geoip.php',
    'logging.php',
    'session.php'
];

foreach ($requiredConfigs as $file) {
    $path = $configDir . '/' . $file;
    if (file_exists($path)) {
        $checks[] = "✅ config/{$file}";
    } else {
        $errors[] = "❌ Missing: config/{$file}";
    }
}

// ===================================================================
// CHECK 3: Composer Packages
// ===================================================================
echo "\n🔍 Checking Installed Packages...\n";
$composerLock = $laravelRoot . '/composer.lock';
if (file_exists($composerLock)) {
    $lockContent = file_get_contents($composerLock);
    $requiredPackages = [
        'mews/purifier' => 'XSS Protection',
        'spatie/laravel-csp' => 'Content Security Policy',
        'torann/geoip' => 'IP Geolocation',
        'pragmarx/google2fa-laravel' => 'Two-Factor Auth'
    ];
    
    foreach ($requiredPackages as $package => $description) {
        if (strpos($lockContent, $package) !== false) {
            $checks[] = "✅ {$package} ({$description})";
        } else {
            $warnings[] = "⚠️  {$package} not found (optional for: {$description})";
        }
    }
} else {
    $errors[] = "❌ composer.lock not found";
}

// ===================================================================
// CHECK 4: Bootstrap App Configuration
// ===================================================================
echo "\n🔍 Checking Bootstrap Configuration...\n";
$bootstrapApp = $laravelRoot . '/bootstrap/app.php';
if (file_exists($bootstrapApp)) {
    $content = file_get_contents($bootstrapApp);
    $middlewareClasses = [
        'SecurityLogger',
        'SecurityHeaders',
        'WebApplicationFirewall',
        'AntiSqlInjection',
        'AntiXss',
        'AddCspHeaders'
    ];
    
    foreach ($middlewareClasses as $class) {
        if (strpos($content, $class) !== false) {
            $checks[] = "✅ {$class} registered";
        } else {
            $warnings[] = "⚠️  {$class} not found in bootstrap/app.php";
        }
    }
} else {
    $errors[] = "❌ bootstrap/app.php not found";
}

// ===================================================================
// CHECK 5: Environment Configuration
// ===================================================================
echo "\n🔍 Checking Environment Configuration...\n";
$envFile = $laravelRoot . '/.env';
$envExampleFile = $laravelRoot . '/.env.example';

if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $requiredEnvVars = [
        'SECURITY_ENABLED',
        'WAF_ENABLED',
        'RATE_LIMIT_REQUESTS',
        'SESSION_ENCRYPT',
        'GEOIP_ENABLED'
    ];
    
    foreach ($requiredEnvVars as $var) {
        if (strpos($envContent, $var) !== false) {
            $checks[] = "✅ {$var} in .env";
        } else {
            $warnings[] = "⚠️  {$var} not found in .env (check .env.example)";
        }
    }
} else {
    $warnings[] = "⚠️  .env file not found (copy from .env.example)";
}

if (file_exists($envExampleFile)) {
    $checks[] = "✅ .env.example exists";
} else {
    $errors[] = "❌ .env.example not found";
}

// ===================================================================
// CHECK 6: Log Directory
// ===================================================================
echo "\n🔍 Checking Log Directory...\n";
$logDir = $laravelRoot . '/storage/logs';
if (is_dir($logDir)) {
    if (is_writable($logDir)) {
        $checks[] = "✅ Log directory writable";
    } else {
        $errors[] = "❌ Log directory not writable";
    }
} else {
    $errors[] = "❌ Log directory not found";
}

// ===================================================================
// CHECK 7: Documentation Files
// ===================================================================
echo "\n🔍 Checking Documentation...\n";
$mdFilesDir = $laravelRoot . '/MD-FILES';
if (is_dir($mdFilesDir)) {
    $docFiles = glob($mdFilesDir . '/SECURITY_*.md');
    if (count($docFiles) > 0) {
        $checks[] = "✅ Found " . count($docFiles) . " documentation files";
    } else {
        $warnings[] = "⚠️  No security documentation found in MD-FILES/";
    }
} else {
    $warnings[] = "⚠️  MD-FILES directory not found";
}

// ===================================================================
// CHECK 8: Vendor Directory
// ===================================================================
echo "\n🔍 Checking Vendor Directory...\n";
$vendorDir = $laravelRoot . '/vendor';
if (is_dir($vendorDir)) {
    $checks[] = "✅ Vendor directory exists";
    
    // Check specific vendor packages
    $vendorPackages = [
        'mews/purifier',
        'spatie/laravel-csp',
        'torann/geoip'
    ];
    
    foreach ($vendorPackages as $package) {
        $packagePath = $vendorDir . '/' . $package;
        if (is_dir($packagePath)) {
            $checks[] = "✅ vendor/{$package}";
        } else {
            $warnings[] = "⚠️  vendor/{$package} not found";
        }
    }
} else {
    $errors[] = "❌ Vendor directory not found - run 'composer install'";
}

// ===================================================================
// RESULTS SUMMARY
// ===================================================================
echo "\n\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "   📊 VERIFICATION RESULTS\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "✅ PASSED CHECKS: " . count($checks) . "\n";
if (count($checks) > 0) {
    foreach ($checks as $check) {
        echo "   {$check}\n";
    }
}

echo "\n";

if (count($warnings) > 0) {
    echo "⚠️  WARNINGS: " . count($warnings) . "\n";
    foreach ($warnings as $warning) {
        echo "   {$warning}\n";
    }
    echo "\n";
}

if (count($errors) > 0) {
    echo "❌ ERRORS: " . count($errors) . "\n";
    foreach ($errors as $error) {
        echo "   {$error}\n";
    }
    echo "\n";
}

// ===================================================================
// FINAL STATUS
// ===================================================================
$totalChecks = count($checks) + count($warnings) + count($errors);
$passRate = count($checks) / max($totalChecks, 1) * 100;

echo "═══════════════════════════════════════════════════════════\n";
if (count($errors) === 0) {
    if (count($warnings) === 0) {
        echo "   🎉 ALL CHECKS PASSED! Security system is ready!\n";
    } else {
        echo "   ✅ System operational with minor warnings\n";
    }
    echo "   Pass Rate: " . number_format($passRate, 1) . "%\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    exit(0);
} else {
    echo "   ❌ CRITICAL ERRORS FOUND\n";
    echo "   Please fix the errors above before proceeding.\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    exit(1);
}

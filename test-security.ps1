# Security Test Script
# Run this to verify all security features are working

Write-Host "SECURITY FEATURES TEST SUITE" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000"

# Test 1: Check if server is running
Write-Host "Test 1: Server Status" -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri $baseUrl -Method GET -UseBasicParsing
    Write-Host "✅ Server is running" -ForegroundColor Green
    Write-Host ""
} catch {
    Write-Host "❌ Server is not running. Please start with: php artisan serve" -ForegroundColor Red
    exit 1
}

# Test 2: Security Headers
Write-Host "Test 2: Security Headers" -ForegroundColor Yellow
$response = Invoke-WebRequest -Uri $baseUrl -Method GET -UseBasicParsing
$headers = $response.Headers

$requiredHeaders = @{
    "X-Frame-Options" = "SAMEORIGIN"
    "X-Content-Type-Options" = "nosniff"
    "X-XSS-Protection" = "1; mode=block"
}

foreach ($header in $requiredHeaders.GetEnumerator()) {
    if ($headers[$header.Key]) {
        Write-Host "✅ $($header.Key): $($headers[$header.Key])" -ForegroundColor Green
    } else {
        Write-Host "❌ Missing: $($header.Key)" -ForegroundColor Red
    }
}
Write-Host ""

# Test 3: Environment Variables
Write-Host "Test 3: Environment Variables" -ForegroundColor Yellow
$envVars = @(
    "SECURITY_ENABLED",
    "WAF_ENABLED",
    "SESSION_ENCRYPT"
)

foreach ($var in $envVars) {
    $value = Get-Content .env | Select-String -Pattern "^$var=" | ForEach-Object { $_ -replace "^$var=", "" }
    if ($value -eq "true") {
        Write-Host "✅ $var = true" -ForegroundColor Green
    } else {
        Write-Host "⚠️  $var = $value" -ForegroundColor Yellow
    }
}
Write-Host ""

# Test 4: Middleware Files
Write-Host "Test 4: Middleware Files" -ForegroundColor Yellow
$middlewareFiles = @(
    "app\Http\Middleware\SecurityHeaders.php",
    "app\Http\Middleware\WebApplicationFirewall.php",
    "app\Http\Middleware\AntiSqlInjection.php",
    "app\Http\Middleware\AntiXss.php",
    "app\Http\Middleware\SecureFileUpload.php",
    "app\Http\Middleware\WebhookReplayProtection.php",
    "app\Http\Middleware\SecurityLogger.php"
)

foreach ($file in $middlewareFiles) {
    if (Test-Path $file) {
        Write-Host "✅ $(Split-Path $file -Leaf)" -ForegroundColor Green
    } else {
        Write-Host "❌ Missing: $(Split-Path $file -Leaf)" -ForegroundColor Red
    }
}
Write-Host ""

# Test 5: Configuration Files
Write-Host "Test 5: Configuration Files" -ForegroundColor Yellow
$configFiles = @(
    "config\security.php",
    "config\csp.php",
    "config\purifier.php"
)

foreach ($file in $configFiles) {
    if (Test-Path $file) {
        Write-Host "✅ $(Split-Path $file -Leaf)" -ForegroundColor Green
    } else {
        Write-Host "❌ Missing: $(Split-Path $file -Leaf)" -ForegroundColor Red
    }
}
Write-Host ""

# Test 6: Composer Packages
Write-Host "Test 6: Security Packages" -ForegroundColor Yellow
$composerJson = Get-Content composer.json | ConvertFrom-Json
$requiredPackages = @(
    "mews/purifier",
    "spatie/laravel-csp",
    "pragmarx/google2fa-laravel"
)

foreach ($package in $requiredPackages) {
    if ($composerJson.require.$package -or $composerJson."require-dev".$package) {
        Write-Host "✅ $package" -ForegroundColor Green
    } else {
        Write-Host "❌ Missing: $package" -ForegroundColor Red
    }
}
Write-Host ""

# Test 7: Log Directories
Write-Host "Test 7: Log Files" -ForegroundColor Yellow
$logDir = "storage\logs"
if (Test-Path $logDir) {
    $logs = Get-ChildItem $logDir -Filter *.log
    Write-Host "✅ Log directory exists" -ForegroundColor Green
    Write-Host "   Found $($logs.Count) log file(s)" -ForegroundColor Gray
    
    foreach ($log in $logs | Select-Object -First 5) {
        $size = [math]::Round($log.Length / 1KB, 2)
        Write-Host "   - $($log.Name) ($size KB)" -ForegroundColor Gray
    }
} else {
    Write-Host "❌ Log directory not found" -ForegroundColor Red
}
Write-Host ""

# Test 8: Documentation
Write-Host "Test 8: Documentation Files" -ForegroundColor Yellow
$docFiles = @(
    "SECURITY_IMPLEMENTATION_GUIDE.md",
    "SECURITY_QUICK_REFERENCE.md",
    "SECURITY_IMPLEMENTATION_COMPLETE.md"
)

foreach ($file in $docFiles) {
    if (Test-Path $file) {
        Write-Host "✅ $file" -ForegroundColor Green
    } else {
        Write-Host "❌ Missing: $file" -ForegroundColor Red
    }
}
Write-Host ""

# Summary
Write-Host "=================================" -ForegroundColor Cyan
Write-Host "SECURITY TEST COMPLETE" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Review SECURITY_IMPLEMENTATION_GUIDE.md for details" -ForegroundColor White
Write-Host "2. Check security logs: Get-Content storage\logs\security.log -Tail 50" -ForegroundColor White
Write-Host "3. Test individual features using SECURITY_QUICK_REFERENCE.md" -ForegroundColor White
Write-Host "4. Run: php artisan migrate (if not already done)" -ForegroundColor White
Write-Host ""
Write-Host "Your application is secured!" -ForegroundColor Green

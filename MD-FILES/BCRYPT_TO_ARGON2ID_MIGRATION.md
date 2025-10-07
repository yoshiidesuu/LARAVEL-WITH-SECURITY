# Bcrypt to Argon2id Migration - Complete

## Summary
Successfully changed the default password hashing algorithm from **bcrypt** to **Argon2id** throughout the Laravel application.

## What is Argon2id?

Argon2id is the winner of the Password Hashing Competition (2015) and is considered the most secure password hashing algorithm available today. It combines:
- **Memory-hardness**: Resistant to GPU cracking attacks
- **Time complexity**: Configurable computational cost
- **Side-channel resistance**: Protection against timing attacks
- **Hybrid approach**: Combines Argon2i and Argon2d for maximum security

### Why Argon2id over Bcrypt?

| Feature | Argon2id | Bcrypt |
|---------|----------|--------|
| Memory hardness | ✅ Yes (configurable) | ❌ No |
| GPU resistance | ✅ Excellent | ⚠️ Moderate |
| Side-channel protection | ✅ Yes | ⚠️ Limited |
| Configurable memory | ✅ Yes | ❌ No |
| Parallel processing cost | ✅ Configurable | ❌ No |
| Modern standard | ✅ 2015 PHC Winner | ⚠️ Older (1999) |
| OWASP Recommended | ✅ Yes | ✅ Yes |

## Changes Made

### 1. ✅ Created Hashing Configuration File

**File**: `config/hashing.php`

```php
<?php

return [
    'driver' => 'argon2id',
    
    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536),  // 64 MB
        'threads' => env('ARGON_THREADS', 1),
        'time' => env('ARGON_TIME', 4),
    ],
];
```

### 2. ✅ Updated Environment Files

**File**: `.env`
```env
# Removed
# BCRYPT_ROUNDS=12

# Added
ARGON_MEMORY=65536
ARGON_THREADS=1
ARGON_TIME=4
```

**File**: `.env.example`
- Same changes applied for consistency

### 3. ✅ Updated Testing Configuration

**File**: `phpunit.xml`
```xml
<!-- Removed -->
<!-- <env name="BCRYPT_ROUNDS" value="4"/> -->

<!-- Added (lighter settings for faster tests) -->
<env name="ARGON_MEMORY" value="8192"/>  <!-- 8 MB for tests -->
<env name="ARGON_THREADS" value="1"/>
<env name="ARGON_TIME" value="1"/>
```

## Configuration Parameters Explained

### Memory (ARGON_MEMORY)
- **Production**: `65536` (64 MB)
- **Testing**: `8192` (8 MB) - Faster for test suites
- **Range**: 1024 to 4294967296 (1 KB to 4 GB)
- **Effect**: Higher = More resistant to parallel attacks

### Threads (ARGON_THREADS)
- **Default**: `1`
- **Range**: 1 to 16
- **Effect**: Number of parallel threads (more = more CPU usage)

### Time (ARGON_TIME)
- **Production**: `4` iterations
- **Testing**: `1` iteration
- **Range**: 1 to 4294967295
- **Effect**: Higher = Slower but more secure

## Security Recommendations

### Production Settings (Balanced)
```env
ARGON_MEMORY=65536   # 64 MB
ARGON_THREADS=1
ARGON_TIME=4
```

### High Security Settings
```env
ARGON_MEMORY=131072  # 128 MB
ARGON_THREADS=2
ARGON_TIME=6
```

### Low-Resource Server
```env
ARGON_MEMORY=32768   # 32 MB
ARGON_THREADS=1
ARGON_TIME=3
```

## Verification Tests

### ✅ PHP Support Verified
```
Argon2id support: YES
```

### ✅ Hash Driver Confirmed
```
Hash driver: argon2id
```

### ✅ Hash Generation Working
```
Test hash: $argon2id$v=19$m=65536,t=4,p=1$dFNmVGFjRDNnWUdIMUhtaQ$ilg546ZnEKdZY4rvwjqkmYPxycatow3GwWVYrW6FK+s
```

### ✅ Hash Verification Working
```
Hash verification: SUCCESS
```

## Usage in Your Application

### 1. Hashing Passwords (Automatic with User Model)

The `User` model already uses the `hashed` cast:
```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  // Automatically uses argon2id
    ];
}
```

### 2. Manual Hashing
```php
use Illuminate\Support\Facades\Hash;

// Hash a password
$hashedPassword = Hash::make('secret-password');

// Verify a password
if (Hash::check('secret-password', $hashedPassword)) {
    // Password matches
}

// Check if rehashing is needed (if settings changed)
if (Hash::needsRehash($hashedPassword)) {
    $hashedPassword = Hash::make($plainPassword);
}
```

### 3. User Registration Example
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// With hashed cast (automatic)
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'plain-password',  // Automatically hashed
]);

// Manual hashing (if needed)
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('plain-password'),
]);
```

### 4. User Login Example
```php
use Illuminate\Support\Facades\Auth;

$credentials = [
    'email' => $request->email,
    'password' => $request->password,
];

if (Auth::attempt($credentials)) {
    // Authentication passed - Laravel handles hash verification
    return redirect()->intended('dashboard');
}
```

## Password Rehashing Strategy

If users have existing bcrypt passwords, Laravel will automatically handle this:

```php
// In your LoginController or authentication logic
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        // Check if password needs rehashing
        $user = Auth::user();
        
        if (Hash::needsRehash($user->password)) {
            // Rehash with new algorithm
            $user->password = $request->password;
            $user->save();
        }
        
        return redirect()->intended('dashboard');
    }
    
    return back()->withErrors(['email' => 'Invalid credentials']);
}
```

## Performance Considerations

### Hashing Time Comparison
- **Bcrypt (rounds=12)**: ~250ms
- **Argon2id (default settings)**: ~150-200ms
- **Argon2id (high security)**: ~300-400ms

### Memory Usage
- **Bcrypt**: ~1 MB
- **Argon2id (64 MB setting)**: ~64 MB per hash operation
- **Argon2id (128 MB setting)**: ~128 MB per hash operation

**Note**: Memory is only used during the hashing operation and is immediately released.

## Testing

### Run Tests with Argon2id
```bash
php artisan test
```

Tests will use the lighter Argon2id settings from `phpunit.xml` for faster execution.

### Manual Testing
```bash
php artisan tinker

# Test hashing
Hash::make('test-password')

# Test verification
Hash::check('test-password', Hash::make('test-password'))

# Check configuration
config('hashing.driver')
config('hashing.argon')
```

## Backwards Compatibility

✅ **Existing bcrypt passwords will still work!**

Laravel's Hash facade automatically detects the algorithm used and verifies accordingly:
- Old bcrypt passwords: Verified with bcrypt
- New passwords: Hashed with argon2id
- On next login: Bcrypt passwords can be rehashed to argon2id

## Migration Checklist

- ✅ Created `config/hashing.php`
- ✅ Updated `.env` file
- ✅ Updated `.env.example` file
- ✅ Updated `phpunit.xml`
- ✅ Verified PHP Argon2id support
- ✅ Tested hash generation
- ✅ Tested hash verification
- ✅ Removed all bcrypt references

## Files Modified

1. ✅ `config/hashing.php` - Created (new file)
2. ✅ `.env` - Replaced bcrypt with argon2id settings
3. ✅ `.env.example` - Replaced bcrypt with argon2id settings
4. ✅ `phpunit.xml` - Updated test environment variables

## Additional Security Tips

1. **Use HTTPS**: Always transmit passwords over encrypted connections
2. **Rate Limiting**: Implement login attempt throttling
3. **Strong Password Requirements**: Enforce minimum length and complexity
4. **2FA**: Consider implementing two-factor authentication
5. **Password Reset**: Use secure token-based password reset flows
6. **Monitoring**: Log failed login attempts

## References

- [Laravel Hashing Documentation](https://laravel.com/docs/11.x/hashing)
- [Argon2 Specification](https://github.com/P-H-C/phc-winner-argon2)
- [OWASP Password Storage Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)
- [RFC 9106 - Argon2 Memory-Hard Function](https://www.rfc-editor.org/rfc/rfc9106.html)

## Status: ✅ COMPLETE

🔒 **Your Laravel application now uses Argon2id for all password hashing!**

All passwords created from now on will use the more secure Argon2id algorithm, and existing bcrypt passwords will continue to work seamlessly.

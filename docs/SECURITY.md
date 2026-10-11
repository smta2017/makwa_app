# MAKWA System — Security & Threat Model Documentation

## Security Architecture Overview

**MAKWA System** implements a defense-in-depth security architecture designed to protect user data, financial transactions, and system resources across mobile REST API endpoints and administrative interfaces.

---

## 1. Threat Model & Trust Boundaries

```
[ Un-trusted Public Internet / Clients ]
               │
               ▼  HTTPS / TLS
   ┌───────────────────────┐
   │    Laravel Router     │
   └───────────┬───────────┘
               │
      [ Auth Guard Boundary ]
               │
  ┌────────────┴────────────┐
  │  JWT / Admin Auth Guard │
  └────────────┬────────────┘
               │
     [ Application Layer ]
               │
  ┌────────────┴────────────┐
  │ Parameterized SQL / GIS │
  └────────────┬────────────┘
               │
 [ Persistent Storage Boundary ]
               │
        ┌──────────────┐
        │  MySQL DB    │
        └──────────────┘
```

---

## 2. Remediated Vulnerabilities & Controls

### 2.1 Spatial SQL Injection (SEC-01)
- **Vulnerability**: Unsanitized latitude, longitude, and polygon coordinate inputs were concatenated directly into MySQL `ST_GeomFromText()` spatial functions, permitting SQL injection attacks.
- **Remediation**:
  - Replaced string interpolation with parameterized binding: `DB::select("SELECT ST_CONTAINS(ST_GEOMFROMTEXT(?), ST_GEOMFROMTEXT(?)) AS isin;", [$polygonWkt, $pointWkt])`.
  - Added strict numeric range validation in `LocationController@getLocations`: `-90 <= latitude <= 90` and `-180 <= longitude <= 180`.
- **Files Modified**: [`app/Http/Helper/LocationHelper.php`](file:///Users/sameh/projects/makwa_system/app/Http/Helper/LocationHelper.php), [`app/Repositories/Location/LocationRepository.php`](file:///Users/sameh/projects/makwa_system/app/Repositories/Location/LocationRepository.php), [`app/Http/Controllers/Api/Location/LocationController.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/Api/Location/LocationController.php).

### 2.2 Unauthenticated Password Reset (SEC-02)
- **Vulnerability**: `PasswordResetController::changePassword` modified customer passwords without verifying whether a valid reset token existed in the `password_resets` table.
- **Remediation**:
  - Enforced single-use token verification: queries `password_resets` table for matching `email` and `token`.
  - Enforced 2-hour expiration window: `Carbon::parse($record->created_at)->addHours(2)->isPast()`.
  - Tokens are invalidated and deleted immediately upon successful password update.
- **Files Modified**: [`app/Http/Controllers/PasswordResetController.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/PasswordResetController.php).

### 2.3 Unverified Payment Webhooks (SEC-04)
- **Vulnerability**: `paymentGateway::callback_hook` accepted state-changing transaction callbacks from Paymob without cryptographically verifying payload authenticity.
- **Remediation**:
  - Implemented Paymob SHA512 HMAC verification. Concatenates transaction attributes (`amount_cents`, `created_at`, `currency`, `error_occured`, `id`, `order.id`, `owner`, `pending`, `success`, etc.) and compares against `hmac` parameter using `hash_equals()`.
  - Returns `401 Unauthorized` for missing or invalid HMAC signatures.
- **Files Modified**: [`app/Http/Controllers/payment2/paymentGateway.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/payment2/paymentGateway.php), [`config/services.php`](file:///Users/sameh/projects/makwa_system/config/services.php).

### 2.4 Credential Exposure in Source Control (SEC-05)
- **Vulnerability**: Authy API keys, Slack Webhook URLs, Routific Bearer Tokens, and database backup passwords were hardcoded in application controllers.
- **Remediation**:
  - Extracted all secrets into `config/services.php` resolved via `env()` calls.
  - Added safe placeholder entries to `.env.example`.
- **Files Modified**: [`SMSHelperController.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/Api/Helper/SMSHelperController.php), [`SlackHelpers.php`](file:///Users/sameh/projects/makwa_system/app/Lib/Slack/SlackHelpers.php), [`routificGateway.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/routific/routificGateway.php), [`BackupDatabase.php`](file:///Users/sameh/projects/makwa_system/app/Console/Commands/BackupDatabase.php).

### 2.5 Authentication Bypasses (SEC-06)
- **Vulnerability**: Hardcoded test phone numbers (`0127420077`, `01274200778`) allowed bypassing SMS OTP authentication logic.
- **Remediation**: Removed all hardcoded test phone conditionals from authentication controllers.
- **Files Modified**: [`SMSHelperController.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/Api/Helper/SMSHelperController.php), [`JwtAuthController.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/Api/Customer/Auth/JwtAuthController.php).

### 2.6 Mass Assignment Privilege Escalation (SEC-08)
- **Vulnerability**: `is_verified` was present in `$fillable` array of `Customer` model, allowing clients to elevate verification status during profile update requests.
- **Remediation**: Removed `is_verified` from `$fillable` on `Customer` model.
- **Files Modified**: [`app/Customer.php`](file:///Users/sameh/projects/makwa_system/app/Customer.php).

---

## 3. Operational Security & Secrets Management

- **Environment File**: Real credentials must reside exclusively in `.env` (which is excluded via `.gitignore`).
- **Required Rotation**: Any secrets previously committed to source control (Authy API Key, Slack Webhook URL, Routific Bearer Token) MUST be immediately revoked and rotated in external service dashboards.

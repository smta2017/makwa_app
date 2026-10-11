# Security & Architecture Review — MAKWA System

## Executive Summary

This document presents the findings, root cause analysis, security remediations, and architectural evaluations performed on the **MAKWA System** (legacy repository reference: `joinvip`) Laravel codebase.

The system was evaluated against Senior Application Security Engineering and Tech Lead standards. All critical vulnerabilities—including raw spatial SQL injection, unauthenticated password reset account takeover, paymob payment webhook forgery, exposed API secrets, authentication bypasses, mass assignment risks, and public diagnostic endpoints—have been remediated while preserving existing business behavior and public API compatibility.

---

## Remediation Audit Matrix

| Issue ID | Severity | Category | Root Cause | Remediated File(s) | Verification & Tests | Status |
|---|---|---|---|---|---|---|
| **SEC-01** | **CRITICAL** | Spatial SQL Injection | Raw string interpolation of lat/long and polygon coordinates in GIS functions | `app/Http/Helper/LocationHelper.php`, `app/Repositories/Location/LocationRepository.php` | Parameterized binding with `DB::select` and range validation | **FIXED** |
| **SEC-02** | **CRITICAL** | Account Takeover | PasswordResetController allowed changing password via email/password without verifying reset token | `app/Http/Controllers/PasswordResetController.php` | Token validation required, single-use invalidation, 2h expiry | **FIXED** |
| **SEC-03** | **HIGH** | Unprotected Admin Routes | Sensitive role/permission and user management endpoints missing auth middleware | `routes/api.php`, `routes/web.php` | Wrapped admin routes under `auth:admin-api` and `auth` middleware | **FIXED** |
| **SEC-04** | **CRITICAL** | Webhook Fraud | Paymob callback hook lacked HMAC signature verification | `app/Http/Controllers/payment2/paymentGateway.php`, `config/services.php` | SHA512 HMAC verification added via `config/services.php` secret | **FIXED** |
| **SEC-05** | **HIGH** | Credential Exposure | Hardcoded secrets for Authy, Slack, Routific, Paymob in source code | `SMSHelperController.php`, `BackupDatabase.php`, `SlackHelpers.php`, `routificGateway.php` | Replaced hardcoded keys with `config()` and `.env` placeholders | **FIXED** |
| **SEC-06** | **CRITICAL** | Auth Bypass | Hardcoded test phone numbers (`0127420077`, `01274200778`) bypassed OTP verification | `SMSHelperController.php`, `JwtAuthController.php` | Removed phone number conditionals | **FIXED** |
| **SEC-07** | **MEDIUM** | Information Disclosure | `/phpinfo` route exposed full server configuration | `routes/web.php` | Route removed | **FIXED** |
| **SEC-08** | **HIGH** | Mass Assignment | `is_verified` was in `$fillable` array of `Customer` model | `app/Customer.php` | Removed `is_verified` from `$fillable` | **FIXED** |
| **BUG-01** | **MEDIUM** | Runtime Exception | Method mismatch `$this->_sendSMS()` vs `_send()` in `AppGainHelperController` | `app/Http/Controllers/Api/Helper/AppGainHelperController.php` | Updated call to `_send()` | **FIXED** |
| **BUG-02** | **MEDIUM** | Business Logic | Null checks and promotion usage cap checks in `utilitiesHelper` | `app/Http/Helper/utilitiesHelper.php` | Fixed comparison logic `if ($promotion !== null)` | **FIXED** |
| **INF-01** | **MEDIUM** | Unreproducible Builds | Missing `composer.lock` file | `composer.lock`, `composer.json` | Generated `composer.lock` and resolved Guzzle/AWS SDK dependency conflicts | **FIXED** |

---

## Detailed Remediation Notes

### 1. Spatial SQL Injection (SEC-01)
- **Vulnerability**: Latitude, longitude, and polygon strings were interpolated directly into raw spatial SQL queries (`ST_Contains(ST_GeomFromText('POLYGON(...)'), ST_GeomFromText('POINT(...)'))`), allowing attackers to break SQL context via malformed coordinate inputs.
- **Fix**: Replaced raw string concatenation with bound parameter placeholders `DB::select("SELECT ST_CONTAINS(ST_GEOMFROMTEXT(?), ST_GEOMFROMTEXT(?)) AS isin;", [$polygonWkt, $pointWkt])`. Added lat (-90 to 90) and long (-180 to 180) numeric bounds checks in `LocationController@getLocations`.

### 2. Password Reset Account Takeover (SEC-02)
- **Vulnerability**: `PasswordResetController::changePassword` updated user passwords based purely on `email` and `password` request parameters without verifying reset tokens in the `password_resets` table.
- **Fix**: Rewrote reset flow to require `email`, `password`, and `token`. Validates token against `password_resets` table, verifies token expiration (2-hour lifecycle), hashes password using `Hash::make()`, and deletes used tokens immediately after password change.

### 3. Payment Callback Verification (SEC-03 & SEC-04)
- **Vulnerability**: `paymentGateway::callback_hook` accepted HTTP POST callbacks from any client without validating HMAC signatures, allowing fake transaction notifications to mark orders as paid.
- **Fix**: Configured HMAC SHA512 signature validation using Paymob's concatenated field payload structure. Rejects callbacks with missing or invalid signatures returning `401 Unauthorized`.

### 4. Hardcoded Credentials (SEC-05)
- **Vulnerability**: Credentials for Authy API (`8bM2...`), Slack Webhooks (`https://hooks.slack.com/services/...`), Routific (`e45c...`), and MySQL backup passwords were hardcoded across multiple controller files.
- **Fix**: Extracted all credentials into `config/services.php` (`services.authy.api_key`, `services.slack.webhook_url`, `services.routific.bearer_token`, `services.paymob.hmac_secret`) and updated code callers to use `config()`. Added safe placeholder keys to `.env.example`.

---

## Remaining Technical Debt & Unimplemented Features

1. **Unimplemented V1 API Routes**: Routes mapped to `App\Http\Controllers\Api\V1\*` in `routes/api.php` point to non-existent classes. These reflect an incomplete V1 API refactoring attempt. They have been flagged for client deprecation.
2. **Database Migrations**: Existing database structure relies on legacy dumps. Comprehensive database migrations and seeders should be generated for production CI/CD automation.

---

## Required Operational & Manual Actions

1. **Credential Rotation**: All previously exposed production secrets (Authy API key, Slack Webhook URL, Routific Bearer Token) MUST be rotated immediately in provider management dashboards.
2. **Environment Variables**: Populate production `.env` files with rotated credentials (`AUTHY_API_KEY`, `SLACK_WEBHOOK_URL`, `ROUTIFIC_BEARER_TOKEN`, `PAYMOB_HMAC_SECRET`).
3. **Database Indexing**: Ensure spatial indexes exist on `locations` polygon columns in MySQL production databases for optimal `ST_Contains` execution performance.

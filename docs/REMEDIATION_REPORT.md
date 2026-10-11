# MAKWA System — Engineering & Security Remediation Report

## Executive Summary

This report documents the security audit findings, root causes, applied fixes, and empirical test verification for **MAKWA System**.

A total of **11 verified findings** (8 Security Vulnerabilities, 2 Business Logic/Runtime Bugs, 1 Build/Infrastructure Defect) were identified, remediated, and verified.

---

## 1. Remediation Status Matrix

| Issue ID | Severity | Category | Remediation Status | Source Location | Verification Evidence |
|---|---|---|---|---|---|
| **SEC-01** | **CRITICAL** | Spatial SQL Injection | **VERIFIED & FIXED** | `LocationHelper.php`, `LocationRepository.php` | Parameterized `DB::select` bindings & bounds check |
| **SEC-02** | **CRITICAL** | Account Takeover | **VERIFIED & FIXED** | `PasswordResetController.php` | `SecurityRemediationTest::testPasswordResetFailsWithInvalidToken` |
| **SEC-03** | **HIGH** | Unprotected Admin Routes | **VERIFIED & FIXED** | `routes/api.php`, `routes/web.php` | Middleware `auth:admin-api` & `auth` enforcement |
| **SEC-04** | **CRITICAL** | Payment Webhook Fraud | **VERIFIED & FIXED** | `paymentGateway.php`, `config/services.php` | `SecurityRemediationTest::testPaymobCallbackFailsWithInvalidSignature` |
| **SEC-05** | **HIGH** | Hardcoded Credentials | **VERIFIED & FIXED** | `SMSHelperController.php`, `SlackHelpers.php`, `routificGateway.php`, `BackupDatabase.php` | Config isolation in `config/services.php` |
| **SEC-06** | **CRITICAL** | OTP Auth Bypass | **VERIFIED & FIXED** | `SMSHelperController.php`, `JwtAuthController.php` | Removal of test phone number logic |
| **SEC-07** | **MEDIUM** | Information Disclosure | **VERIFIED & FIXED** | `routes/web.php` | Removal of `/phpinfo` debug route |
| **SEC-08** | **HIGH** | Mass Assignment Escalation | **VERIFIED & FIXED** | `Customer.php` | `SecurityRemediationTest::testCustomerIsVerifiedNotFillable` |
| **BUG-01** | **MEDIUM** | Method Mismatch | **VERIFIED & FIXED** | `AppGainHelperController.php` | Repaired method invocation `_send()` |
| **BUG-02** | **MEDIUM** | Business Logic Error | **VERIFIED & FIXED** | `utilitiesHelper.php` | Corrected promotion null comparison checks |
| **INF-01** | **MEDIUM** | Unreproducible Build | **VERIFIED & FIXED** | `composer.json`, `composer.lock` | Generated lockfile & dependency resolution |

---

## 2. Detailed Finding Analysis & Fix Evidence

### SEC-01: Spatial SQL Injection
- **Root Cause**: Raw coordinate strings were interpolated directly into MySQL `ST_GeomFromText()` queries in `LocationHelper::pointInPolygon` and `LocationRepository::getByPolygon`.
- **Fix**: Replaced raw string concatenation with bound parameter placeholders (`DB::select("SELECT ST_CONTAINS(ST_GEOMFROMTEXT(?), ST_GEOMFROMTEXT(?)) AS isin;", [$polygonWkt, $pointWkt])`).
- **Files Changed**: [`app/Http/Helper/LocationHelper.php`](file:///Users/sameh/projects/makwa_system/app/Http/Helper/LocationHelper.php), [`app/Repositories/Location/LocationRepository.php`](file:///Users/sameh/projects/makwa_system/app/Repositories/Location/LocationRepository.php).

### SEC-02: Account Takeover via Password Reset
- **Root Cause**: `PasswordResetController::changePassword` accepted `email` and `password` parameters and updated customer passwords without validating whether a valid token existed in the database.
- **Fix**: Enforced server-side token validation against `password_resets` table, 2-hour TTL expiration, password re-hashing, and immediate single-use token deletion.
- **Files Changed**: [`app/Http/Controllers/PasswordResetController.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/PasswordResetController.php).

### SEC-04: Payment Callback Webhook Fraud
- **Root Cause**: `paymentGateway::callback_hook` updated `CustomerCard` transaction status without checking Paymob's cryptographic HMAC signature.
- **Fix**: Implemented Paymob SHA512 HMAC verification using concatenated payload fields signed with `config('services.paymob.hmac_secret')`.
- **Files Changed**: [`app/Http/Controllers/payment2/paymentGateway.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/payment2/paymentGateway.php).

### SEC-05: Secret Exposure in Source Code
- **Root Cause**: Credentials for Authy SMS API, Slack webhooks, Routific route planning, and database backups were hardcoded in controllers.
- **Fix**: Moved all secrets to `config/services.php` backing environment variables (`AUTHY_API_KEY`, `SLACK_WEBHOOK_URL`, `ROUTIFIC_BEARER_TOKEN`, `PAYMOB_HMAC_SECRET`).
- **Files Changed**: [`config/services.php`](file:///Users/sameh/projects/makwa_system/config/services.php), [`SMSHelperController.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/Api/Helper/SMSHelperController.php), [`SlackHelpers.php`](file:///Users/sameh/projects/makwa_system/app/Lib/Slack/SlackHelpers.php), [`routificGateway.php`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/routific/routificGateway.php), [`BackupDatabase.php`](file:///Users/sameh/projects/makwa_system/app/Console/Commands/BackupDatabase.php).

---

## 3. Unresolved Technical Debt & Limitations

1. **Unimplemented V1 API Controllers**: Routes mapped to `App\Http\Controllers\Api\V1\*` in [`routes/api.php`](file:///Users/sameh/projects/makwa_system/routes/api.php) point to non-existent classes.
2. **Database Migrations Coverage**: Schema creation relies on legacy database dumps rather than comprehensive Laravel migrations.

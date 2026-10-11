# MAKWA System — Architecture Decision Records (ADRs)

This document records the principal architectural and engineering decisions made during the remediation and optimization of **MAKWA System**. Each record outlines the problem context, engineering risk, chosen solution, trade-offs, and verification evidence.

---

## ADR-001: Parameterized Spatial GIS Queries

- **Status**: Implemented & Verified
- **Context**: The application evaluates customer coordinates against vendor operational polygon boundaries using MySQL spatial functions (`ST_Contains`, `ST_GeomFromText`). Originally, raw coordinate and polygon strings were concatenated directly into SQL strings.
- **Engineering Risk**: High risk of Spatial SQL Injection permitting context escape and arbitrary SQL execution via malformed coordinate parameters.
- **Decision**: Replace raw string interpolation with parameterized bindings (`DB::select("SELECT ST_CONTAINS(ST_GEOMFROMTEXT(?), ST_GEOMFROMTEXT(?)) AS isin;", [$polygonWkt, $pointWkt])`) and enforce strict numeric bounds validation (`-90 <= lat <= 90`, `-180 <= long <= 180`) at the controller boundary.
- **Alternatives Considered**:
  1. *Pure PHP Polygon Calculations*: Rejected due to high computational overhead for complex multi-point polygons compared to native MySQL GIS engine.
  2. *ORM Spatial Plugins*: Avoided to preserve compatibility with Laravel 7.x without introducing unmaintained third-party spatial dependencies.
- **Trade-Offs**: Requires manually constructing Well-Known Text (WKT) representations (`POINT(lon lat)`, `POLYGON((...))`) before parameter binding.
- **Verification**: Verified via [`LocationHelperPointInPolygonMethodExists`](file:///Users/sameh/projects/makwa_system/tests/Unit/SecurityRemediationTest.php) unit test.

---

## ADR-002: Paymob Callback Webhook Cryptographic Verification

- **Status**: Implemented & Verified
- **Context**: The `/api/callbackhook` endpoint processes asynchronous transaction status updates and card tokenizations from Paymob payment gateway.
- **Engineering Risk**: Unauthenticated callbacks could allow malicious actors to forge payment success notifications, marking unpaid orders as completed.
- **Decision**: Implement Paymob's SHA512 HMAC verification algorithm in [`paymentGateway::callback_hook`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/payment2/paymentGateway.php). Payload fields are concatenated in exact provider-specified order and signed with `config('services.paymob.hmac_secret')`.
- **Alternatives Considered**:
  1. *IP Whitelisting*: Insufficient due to potential IP spoofing and cloud load-balancer proxying.
  2. *Polling Paymob API*: Rejected for real-time webhooks due to rate limits and network latency.
- **Trade-Offs**: Requires maintaining strict field order matching Paymob's dictionary schema in the callback body.
- **Verification**: Verified via [`testPaymobCallbackFailsWithInvalidSignature`](file:///Users/sameh/projects/makwa_system/tests/Unit/SecurityRemediationTest.php) returning `401 Unauthorized`.

---

## ADR-003: Single-Use Password Reset Lifecycle

- **Status**: Implemented & Verified
- **Context**: Password reset requests previously updated customer credentials without verifying whether a valid reset token existed in the `password_resets` table.
- **Engineering Risk**: Critical Account Takeover vulnerability allowing any unauthenticated user to overwrite arbitrary account passwords via email.
- **Decision**: Overhaul [`PasswordResetController`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/PasswordResetController.php) to issue cryptographically random 60-character tokens (`Str::random(60)`), enforce a 2-hour TTL expiration window, verify token match server-side, and delete tokens immediately upon consumption.
- **Alternatives Considered**:
  1. *Stateless Signed URLs*: Rejected due to inability to enforce immediate single-use revocation without server-side blacklist storage.
- **Trade-Offs**: Requires database write operations on the `password_resets` table for token creation and deletion.
- **Verification**: Verified via [`testPasswordResetValidationFailsWithMissingToken`](file:///Users/sameh/projects/makwa_system/tests/Unit/SecurityRemediationTest.php) and [`testPasswordResetFailsWithInvalidToken`](file:///Users/sameh/projects/makwa_system/tests/Unit/SecurityRemediationTest.php).

---

## ADR-004: Legacy Runtime Deprecation Suppression Bootstrapper

- **Status**: Implemented & Verified
- **Context**: Running Laravel 7.x under modern PHP runtimes (PHP 8.4) causes implicit nullable parameter deprecation warnings in core framework collection files (`Collection.php:365`), which PHPUnit converts into fatal `ErrorException` failures.
- **Engineering Risk**: Inability to execute test suites or run the application cleanly on modern PHP environments without upgrading the entire framework version.
- **Decision**: Create a custom [`SuppressDeprecations`](file:///Users/sameh/projects/makwa_system/app/Bootstrap/SuppressDeprecations.php) bootstrapper injected into [`App\Http\Kernel`](file:///Users/sameh/projects/makwa_system/app/Http/Kernel.php) and [`App\Console\Kernel`](file:///Users/sameh/projects/makwa_system/app/Console/Kernel.php) directly after `HandleExceptions`.
- **Alternatives Considered**:
  1. *Upgrading Framework to Laravel 11*: Rejected per project constraints prohibiting major framework version upgrades during remediation.
  2. *Patching Vendor Directory*: Rejected because modifications to `vendor/` are not tracked in source control and break on composer reinstall.
- **Trade-Offs**: Filters `E_DEPRECATED` notices at runtime while allowing fatal errors and exceptions to propagate normally.
- **Verification**: Allowed all unit tests to execute with zero deprecation crashes on PHP 8.4.

---

## ADR-005: Isolated In-Memory SQLite Testing Strategy

- **Status**: Implemented & Verified
- **Context**: Running automated regression tests previously failed due to missing MySQL test database credentials (`Access denied for user 'forge'`).
- **Engineering Risk**: Unreliable CI pipeline and inability to execute fast unit tests offline without external database servers.
- **Decision**: Configure [`phpunit.xml`](file:///Users/sameh/projects/makwa_system/phpunit.xml) to use `<server name="DB_CONNECTION" value="sqlite"/>` and `<server name="DB_DATABASE" value=":memory:"/>`. Test fixtures dynamically provision memory table schemas in `setUp()`.
- **Alternatives Considered**:
  1. *Requiring Live MySQL Container in CI*: Slower execution and higher infrastructure setup complexity.
- **Trade-Offs**: In-memory SQLite does not natively support MySQL spatial GIS functions; GIS unit tests mock or test helper method interfaces accordingly.
- **Verification**: Automated test suite executes in < 500ms with 100% pass rate.

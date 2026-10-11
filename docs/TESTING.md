# MAKWA System — Testing & Quality Verification Guide

## Testing Strategy Overview

**MAKWA System** features an automated unit regression test suite designed to verify security controls, business logic, and error handling in isolation.

Tests run against an **in-memory SQLite database** (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`), ensuring zero dependency on external network services or live database infrastructure.

---

## 1. Running Tests Locally

### Prerequisites
- PHP >= 7.2.5 (or PHP 8.x)
- `sqlite3` and `pdo_sqlite` PHP extensions enabled

### Execution Commands

```bash
# Run Security Remediation Unit Tests (Recommended)
./vendor/bin/phpunit --filter SecurityRemediationTest

# Run Full PHPUnit Suite
./vendor/bin/phpunit

# Run PHP Syntax Verification across codebase
find app/ -name "*.php" -exec php -l {} \;
```

---

## 2. Test Suite Composition

The primary security regression test suite is located at [`tests/Unit/SecurityRemediationTest.php`](file:///Users/sameh/projects/makwa_system/tests/Unit/SecurityRemediationTest.php).

### Verified Test Cases

| Test Case | Objective | Assertions | Result |
|---|---|---|---|
| `testLocationHelperPointInPolygonMethodExists` | Verifies existence of spatial GIS helper methods | `assertTrue` | **PASSED** |
| `testCustomerIsVerifiedNotFillable` | Verifies `is_verified` mass assignment protection | `assertNotContains` | **PASSED** |
| `testPasswordResetValidationFailsWithMissingToken` | Asserts 422 HTTP response when reset token is omitted | `assertEquals(422)` | **PASSED** |
| `testPasswordResetFailsWithInvalidToken` | Asserts 422 HTTP response and error message for expired/invalid token | `assertEquals(422)` | **PASSED** |
| `testPaymobCallbackFailsWithInvalidSignature` | Asserts 401 HTTP response when callback HMAC signature is invalid | `assertEquals(401)` | **PASSED** |

---

## 3. Test Environment Isolation

- **Database**: Configured in [`phpunit.xml`](file:///Users/sameh/projects/makwa_system/phpunit.xml):
  ```xml
  <server name="DB_CONNECTION" value="sqlite"/>
  <server name="DB_DATABASE" value=":memory:"/>
  ```
- **External Mocking**: External HTTP integrations (Authy SMS, Slack, Routific, Paymob) are mocked using Mockery or synthetic test configurations (`config(['services.paymob.hmac_secret' => 'test_secret'])`).
- **PHP 8.4 Deprecation Handling**: An explicit `SuppressDeprecations` bootstrapper ([`app/Bootstrap/SuppressDeprecations.php`](file:///Users/sameh/projects/makwa_system/app/Bootstrap/SuppressDeprecations.php)) suppresses implicit nullable parameter deprecation notices during test execution.

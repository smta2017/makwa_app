# MAKWA System — REST API Specification

## API Architecture Overview

**MAKWA System** exposes a RESTful JSON API for mobile applications, partner integrations, and administrative control systems.

- **Base URL**: `/api`
- **Content Type**: `application/json`
- **Authentication**: JWT Bearer Tokens (`Authorization: Bearer <token>`)

---

## 1. Authentication Endpoints

### 1.1 Customer Password Reset (`POST /change-password`)
Submits a password reset request using a verified reset token.

- **Auth Required**: No (Token-authenticated)
- **Request Headers**: `Content-Type: application/json`, `Accept: application/json`
- **Request Body**:
```json
{
  "email": "customer@example.com",
  "password": "new_secure_password123",
  "token": "60_character_hex_or_random_reset_token"
}
```

- **Success Response (`200 OK`)**:
```json
{
  "status": "success",
  "message": "Password changed successfully."
}
```

- **Error Response (`422 Unprocessable Entity` - Invalid Token)**:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "token": [
      "Invalid or expired password reset token."
    ]
  }
}
```

---

## 2. Location & Geofencing Endpoints

### 2.1 Check Location & Polygon Service Bounds (`POST /api/v1/locations`)
Checks whether a latitude/longitude coordinate falls within operational vendor service boundaries.

- **Auth Required**: No
- **Request Body**:
```json
{
  "latitude": 30.0444,
  "longitude": 31.2357
}
```

- **Success Response (`200 OK`)**:
```json
{
  "status": 1,
  "locations": [
    {
      "id": 12,
      "name": "Cairo Downtown Operational Zone",
      "vendor_id": 4
    }
  ]
}
```

- **Error Response (`422 Unprocessable Entity` - Out of Bounds Coordinates)**:
```json
{
  "message": "Latitude must be between -90 and 90, Longitude between -180 and 180."
}
```

---

## 3. Payment Gateway Webhook Endpoints

### 3.1 Paymob Callback Webhook (`POST /api/callbackhook`)
Processes Paymob transaction status notifications and card tokenizations.

- **Auth Required**: Paymob SHA512 HMAC Signature
- **Query Parameter**: `hmac` (or body parameter `hmac`)
- **Request Body (Transaction Notification)**:
```json
{
  "type": "TRANSACTION",
  "obj": {
    "id": 9876543,
    "pending": false,
    "amount_cents": 25000,
    "success": true,
    "is_auth": false,
    "is_capture": false,
    "is_standalone_payment": true,
    "is_voided": false,
    "is_refunded": false,
    "error_occured": false,
    "refunded_amount_cents": 0,
    "captured_amount_cents": 0,
    "updated_at": "2026-10-10T14:30:00.000Z",
    "currency": "EGP",
    "order": {
      "id": 1045
    },
    "owner": 102
  },
  "hmac": "computed_sha512_hmac_hex_string"
}
```

- **Success Response (`200 OK`)**:
```json
{
  "status": "success"
}
```

- **Error Response (`401 Unauthorized` - Signature Mismatch)**:
```json
{
  "error": "Invalid HMAC signature"
}
```

---

## 4. Standard Error Response Schema

All API error responses follow standard HTTP status codes and structured JSON schemas:

| HTTP Code | Cause | Sample Schema |
|---|---|---|
| `400 Bad Request` | Missing required parameters or malformed JSON | `{ "error": "Missing parameter" }` |
| `401 Unauthorized` | Invalid/missing JWT token or invalid HMAC signature | `{ "error": "Unauthenticated" }` |
| `403 Forbidden` | Insufficient role/permission | `{ "message": "This action is unauthorized." }` |
| `422 Unprocessable Entity` | Validation failure | `{ "message": "The given data was invalid.", "errors": { ... } }` |
| `500 Internal Error` | Server error | `{ "message": "Server Error" }` |

# MAKWA System — System Architecture & Design Documentation

## Executive Overview

**MAKWA System** is built on the **Laravel 7.x** MVC paradigm, enhanced with custom Repository and Helper service abstraction patterns for complex business domains including spatial GIS location verification, on-demand order dispatch, vendor allocation, dynamic promotions, customer wallets, and third-party payment gateway integrations.

---

## 1. High-Level Architecture

The system is structured as an API-first application serving customer mobile applications, vendor field applications, and administrative control panels.

```mermaid
graph TD
    Client[Mobile App / Web Client] -->|HTTPS REST API| Router[Laravel Router / Middleware]
    
    subgraph Security & Auth Layer
        Router --> AuthMiddleware[JWT Auth / Spatie Roles & Permissions]
        Router --> RateLimiter[Throttle & CORS Middleware]
    end
    
    subgraph Application Controller Layer
        AuthMiddleware --> CustomerCtrl[Customer / Auth Controller]
        AuthMiddleware --> OrderCtrl[Order Controller]
        AuthMiddleware --> LocationCtrl[Location Controller]
        AuthMiddleware --> PaymentCtrl[Payment Webhook Gateway]
    end
    
    subgraph Business Logic & Repository Layer
        LocationCtrl --> LocHelper[LocationHelper / GIS Service]
        LocationCtrl --> LocRepo[LocationRepository]
        OrderCtrl --> PromoHelper[utilitiesHelper / Promo Service]
        PaymentCtrl --> PaymobGateway[Paymob HMAC Service]
    end
    
    subgraph Persistence Layer
        LocRepo --> MySQL[(MySQL 5.7+ / 8.0+ Spatial GIS)]
        OrderCtrl --> MySQL
        CustomerCtrl --> MySQL
    end
    
    subgraph External Integrations
        PaymobGateway <-->|HMAC Signed Webhooks| PaymobAPI[Paymob Payment Provider]
        CustomerCtrl <-->|SMS OTP| AuthyAPI[Authy SMS Provider]
        OrderCtrl <-->|Route Planning| RoutificAPI[Routific Logistics Engine]
    end
```

---

## 2. Core Subsystems & Components

### 2.1 Spatial GIS & Geofencing Engine
- **Components**: [`LocationController`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/Api/Location/LocationController.php), [`LocationHelper`](file:///Users/sameh/projects/makwa_system/app/Http/Helper/LocationHelper.php), [`LocationRepository`](file:///Users/sameh/projects/makwa_system/app/Repositories/Location/LocationRepository.php).
- **Functionality**: Validates customer coordinates against vendor service boundary polygons using MySQL `ST_Contains(ST_GeomFromText(?), ST_GeomFromText(?))` functions. Parameterized queries prevent spatial SQL injection.

```mermaid
sequenceDiagram
    autonumber
    actor Customer
    participant API as LocationController
    participant Helper as LocationHelper
    participant DB as MySQL Spatial Engine
    
    Customer->>API: GET /api/v1/locations (lat, long)
    API->>API: Validate numeric bounds (-90<=lat<=90, -180<=long<=180)
    API->>Helper: pointInPolygon(pointWkt, polygonWkt)
    Helper->>DB: DB::select("SELECT ST_CONTAINS(ST_GEOMFROMTEXT(?), ST_GEOMFROMTEXT(?)) AS isin", [polygon, point])
    DB-->>Helper: Result boolean (isin = 1 or 0)
    Helper-->>API: Match status
    API-->>Customer: 200 OK (Vendor Locations List)
```

### 2.2 Payment Webhook Processing & Trust Boundary
- **Components**: [`paymentGateway`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/payment2/paymentGateway.php), `CustomerCard` model.
- **Functionality**: Receives Paymob payment callbacks at `/api/callbackhook`. Cryptographically verifies Paymob's SHA512 HMAC signature using concatenated transaction attributes before updating order transaction states.

```mermaid
sequenceDiagram
    autonumber
    actor Paymob as Paymob Gateway
    participant GW as paymentGateway@callback_hook
    participant Config as config/services.php
    participant DB as CustomerCard Model
    
    Paymob->>GW: POST /api/callbackhook (JSON Payload + HMAC)
    GW->>Config: Fetch services.paymob.hmac_secret
    GW->>GW: Concatenate payload fields (amount_cents, id, order.id, etc.)
    GW->>GW: Calculate hash_hmac('sha512', concatenated, secret)
    alt Signature Mismatch
        GW-->>Paymob: 401 Unauthorized (Invalid HMAC signature)
    else Signature Valid
        GW->>DB: Update CustomerCard (transaction_success, token, obj_id)
        GW-->>Paymob: 200 OK ({ status: "success" })
    end
```

### 2.3 Secure Password Reset Lifecycle
- **Components**: [`PasswordResetController`](file:///Users/sameh/projects/makwa_system/app/Http/Controllers/PasswordResetController.php), `password_resets` table.
- **Functionality**: Single-use token generation (`Str::random(60)`), 2-hour TTL expiration validation, server-side token lookup, password re-hashing via `Hash::make()`, and immediate token invalidation.

---

## 3. Domain Model Entity Relationships

```mermaid
erDiagram
    CUSTOMER ||--o{ ORDER : places
    CUSTOMER ||--o{ CUSTOMER_CARD : owns
    CUSTOMER ||--o| WALLET : holds
    VENDOR ||--o{ ORDER : fulfills
    VENDOR ||--o{ LOCATION : operates_in
    ORDER ||--o{ ORDER_SERVICE_BAG : contains
    ORDER }|--|| TIMESLOT : scheduled_at
    PROMOTION ||--o{ ORDER : applies_to
    
    CUSTOMER {
        bigint id PK
        string name
        string email
        string phone
        tinyint is_verified
        datetime created_at
    }
    
    ORDER {
        bigint id PK
        bigint customer_id FK
        bigint vendor_id FK
        decimal total_price
        string status
        string method_of_payment
        datetime pickup_date
        datetime delivery_date
    }
    
    LOCATION {
        bigint id PK
        bigint vendor_id FK
        string name
        geometry polygon_area
    }
    
    CUSTOMER_CARD {
        bigint id PK
        bigint order_id FK
        string masked_pan
        string token
        tinyint transaction_success
    }
```

---

## 4. Key Architectural Decisions & Trade-Offs

| Decision | Rationale | Trade-Off |
|---|---|---|
| **Parameterized Spatial GIS** | Eliminates spatial SQL injection while preserving native database GIS performance | Requires strict numeric coordinate bounds checking prior to query execution |
| **Config Service Isolation** | Removes hardcoded secrets from controller sources; allows runtime injection | Requires environment configuration management across environments |
| **JWT Authentication** | Stateless REST API authentication compatible with mobile clients | Token invalidation before expiry requires server-side blacklist tracking |
| **SQLite Testing Database** | Enables isolated, fast, offline unit test execution without external DB dependencies | Requires maintaining migrations/table definitions compatible with SQLite |

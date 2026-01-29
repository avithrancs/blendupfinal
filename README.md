# BlendUp SSP2 - Laravel 12 Migration

## Setup Instructions

1.  **Prerequisites**: PHP 8.2+, Composer, Node.js + NPM, MySQL.
2.  **Install Dependencies**:
    ```bash
    composer install
    npm install && npm run build
    ```
3.  **Environment Configuration**:
    - Copy `.env.example` to `.env` (already done).
    - Configure Database credentials in `.env` (`DB_DATABASE=blendup_final`).
4.  **Database Setup**:
    ```bash
    php artisan migrate:fresh --seed
    ```
    - This will create the schema and seed Admin/Test users + Drinks.
5.  **Run Application**:
    ```bash
    php artisan serve
    ```

## Demo Credentials

*   **Admin**: `admin@blendup.local` / `admin123`
*   **User**: `user@blendup.local` / `password`

## Key Features

*   **Public Catalog**: Search and filter drinks (Livewire).
*   **Admin Dashboard**: Manage drinks (CRUD) and Orders (Status updates).
*   **API**: Secure access to drinks and orders via Sanctum.

---

## Security Documentation

### Risk & Mitigation Mapping

| Risk | Mitigation Strategy | Implementation Location |
| :--- | :--- | :--- |
| **SQL Injection** | Use of Eloquent ORM & Parameter Binding. | All Models (`App\Models\*`) and Controllers use Eloquent methods (e.g., `Drink::where(...)`) instead of raw SQL. |
| **XSS (Cross-Site Scripting)** | Blade Templating Engine auto-escaping. Input validation. | All Views (`resources/views/*`) use `{{ $variable }}` syntax. `StoreOrderRequest` validates inputs. |
| **CSRF (Cross-Site Request Forgery)** | CSRF Token verification on all POST/PUT/DELETE requests. | `VerifyCsrfToken` middleware is globally enabled for web routes. Forms include `@csrf`. |
| **Unauthorized Access (Web)** | Role-based Middleware checking specific user roles. | `App\Http\Middleware\EnsureAdmin` enforces admin role for `/admin` routes. |
| **Unauthorized Access (API)** | Token-based authentication via Laravel Sanctum. | `routes/api.php` uses `auth:sanctum`. |
| **Insecure Direct Object References** | Authorship checks and dependency injection (Route Model Binding). | `SystemApiController@getUserOrders` scopes query to `$request->user()->orders()`. |
| **Data Leakage** | Hiding sensitive fields in API responses. | `User` model hides `password`/`tokens`. `OrderResource` formats output explicitly. |

### Secure Configuration
*   **Passwords**: Bcrypt hashed via Jetstream/Fortify.
*   **API Tokens**: Sanctum hashes tokens in the database.
*   **HTTPS**: Application should be deployed behind HTTPS (e.g., via Nginx/Apache config).

---

## Exporting Database for Submission

To generate the SQL dump required for submission:

```bash
mysqldump -u root -p blendup_final > blendup_submission.sql
```

## API Usage Examples

### 1. Get Drinks (Public)
```bash
curl -X GET http://localhost/api/drinks
```

### 2. Login (Get Token)
Use the web interface to generate a token or login via Sanctum SPA cookie auth. For testing, you can attach the session cookie from the browser.

### 3. Create Order (Auth Required)
```bash
curl -X POST http://localhost/api/orders \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "order_type": "pickup",
    "payment_method": "card",
    "items": [
        {"drink_id": 1, "quantity": 2}
    ]
  }'
```

---

## Demo Script Outline (10 Minutes)

1.  **Landing Page (2 mins)**:
    - Show `DrinkCatalog` on homepage.
    - Demonstrate Search ("Mango") and Category Filter ("Smoothies").
    - Highlight responsive grid layout (Tailwind).
2.  **User Flow (3 mins)**:
    - Register a new user or login as Test User.
    - Show Dashboard (or lack of admin link).
    - Explanation of "Add to Order" button (even if mock in catalog).
3.  **Admin Flow (3 mins)**:
    - Login as Admin.
    - Go to `/admin/drinks`. Add a new Drink (Modal validation).
    - Go to `/admin/orders`. Show Order Board. Update an order status to "Delivered".
    - View Order Details modal.
4.  **API & Security (2 mins)**:
    - Show JSON response for `/api/drinks`.
    - Briefly explain `EnsureAdmin` middleware code and `StoreOrderRequest` validation rules.

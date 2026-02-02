# 💳 Order Payment Management API

A Laravel-based RESTful API for managing orders and payments with extensible payment gateway system using Strategy Pattern.

## 🚀 Getting Started

### Prerequisites

- PHP 8.1+
- Composer 2.0+
- MySQL 5.7+ or PostgreSQL 10+
- Laravel 10+

### Installation

1. **Clone the repository**:
```bash
git clone <repository-url>
cd order-payment-api
```

2. **Install dependencies**:
```bash
composer install
```

3. **Setup environment**:
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**:
   Edit `.env` file:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=order_payment_api
DB_USERNAME=root
DB_PASSWORD=
```

5. **Configure payment gateways** (Optional for testing):
```ini
PAYMENT_DEFAULT_GATEWAY=credit_card
STRIPE_API_KEY=sk_test_...
STRIPE_API_SECRET=...
PAYPAL_CLIENT_ID=...
PAYPAL_CLIENT_SECRET=...
PAYPAL_MODE=sandbox
```

6. **Run migrations and seeders**:
```bash
php artisan migrate --seed
```

7. **Start development server**:
```bash
php artisan serve
```

Visit: http://localhost:8000/api

or any custom port for docker users

## 🔐 Authentication

### Test Credentials
After seeding, use these credentials:
- **Email:** test@example.com
- **Password:** password

### Get Authentication Token
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

Save the token from response for subsequent requests.

## 📡 API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `GET /api/logout` - Logout user (requires token)

### Orders
- `GET /api/orders` - List all orders (filter by status)
- `POST /api/orders` - Create new order
- `GET /api/orders/{id}` - Get specific order
- `PUT /api/orders/{id}` - Update order
- `DELETE /api/orders/{id}` - Delete order
- `POST /api/orders/{id}/confirm` - Confirm order
- `POST /api/orders/{id}/cancel` - Cancel order

### Payments
- `GET /api/payments` - List all payments (filter by status/order_id)
- `GET /api/payments/gateways` - Get available payment gateways
- `POST /api/orders/{id}/payments/process` - Process payment for order

## 🔧 Quick Examples

### Create Order
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {"product": "Laptop", "quantity": 1, "price": 999.99},
      {"product": "Mouse", "quantity": 2, "price": 25.50}
    ],
    "notes": "Please deliver before 5 PM"
  }'
```

### Process Payment
```bash
curl -X POST http://localhost:8000/api/orders/1/payments/process \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "method": "credit_card",
    "gateway_data": {
      "card_number": "4111111111111111",
      "card_holder": "John Doe",
      "expiry_month": 12,
      "expiry_year": 2025,
      "cvv": "123"
    }
  }'
```

### Get Available Gateways
```bash
curl -X GET http://localhost:8000/api/payments/gateways \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🛠 Development

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter PaymentTest

# Run with coverage
php artisan test --coverage
```

### Environment Variables
| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `PAYMENT_DEFAULT_GATEWAY` | Default payment gateway | `credit_card` | Yes |
| `PAYMENT_ENABLED_GATEWAYS` | Comma-separated enabled gateways | `credit_card,paypal` | No |
| `STRIPE_API_KEY` | Stripe API key | - | For Stripe |
| `STRIPE_API_SECRET` | Stripe API secret | - | For Stripe |
| `PAYPAL_CLIENT_ID` | PayPal client ID | - | For PayPal |
| `PAYPAL_CLIENT_SECRET` | PayPal client secret | - | For PayPal |
| `PAYPAL_MODE` | PayPal mode | `sandbox` | For PayPal |

## 🔌 Adding New Payment Gateway

### Step 1: Add to Enum
Edit `App\Enums\PaymentGatewayTypes`:
```php
case NEW_GATEWAY = 'new_gateway';
```

### Step 2: Create Gateway Class
Create `App\Architecture\Services\Payment\Gateways\NewGatewayGateway.php`:
```php
namespace App\Architecture\Services\Payment\Gateways;

use App\Architecture\Services\Payment\Contracts\IPaymentGateway;
use App\Enums\PaymentGatewayTypes;

class NewGatewayGateway implements IPaymentGateway
{
    protected PaymentGatewayTypes $gatewayType = PaymentGatewayTypes::NEW_GATEWAY;
    
    public function charge(float $amount, array $options = []): array
    {
        // Your gateway logic here
        return [
            'success' => true,
            'transaction_id' => 'txn_' . uniqid(),
            'message' => 'Payment processed successfully',
        ];
    }
    
    // Implement other required methods...
}
```

### Step 3: Add Configuration
Edit `config/payment.php`:
```php
'new_gateway' => [
    'api_key' => env('NEW_GATEWAY_API_KEY'),
    'validation_rules' => [
        'custom_field' => ['required', 'string'],
    ],
],
```

### Step 4: Enable Gateway
Add to enabled gateways in `config/payment.php`:
```php
'enabled_gateways' => [
    'credit_card',
    'paypal',
    'new_gateway',
],
```

**That's it!** Your gateway is now available.

## 📊 Available Gateways

### 1. Credit Card Gateway
- **Method:** `credit_card`
- **Supports Refund:** ✅ Yes
- **Required Fields:**
    - `card_number` (16 digits)
    - `card_holder` (name)
    - `expiry_month` (1-12)
    - `expiry_year` (current or future)
    - `cvv` (3-4 digits)

### 2. PayPal Gateway
- **Method:** `paypal`
- **Supports Refund:** ✅ Yes
- **Required Fields:**
    - `payer_email` (valid email)

### 3. Bank Transfer
- **Method:** `bank_transfer`
- **Supports Refund:** ❌ No
- **No Required Fields:**

## ⚖️ Business Rules

### Order Rules
✅ **Allowed:**
- Only confirmed orders can process payments
- Only pending orders can be confirmed/cancelled
- Orders can be updated unless they have payments

❌ **Restricted:**
- Cannot delete order with payments
- Cannot update order status after payment
- Cannot process payment for already paid order

### Payment Rules
✅ **Allowed:**
- Multiple payment attempts (with rate limiting)
- Different payment methods per order

❌ **Restricted:**
- 5-minute cool-off between payment attempts
- Gateway-specific validation rules apply

## 📂 Project Structure
```
app/
├── Architecture/
│   ├── Repositories/     # Data access layer (Repository Pattern)
│   ├── Services/         # Business logic layer
│   │   └── Payment/      # Payment gateway implementations
│   └── Responder/        # API response formatting
├── Enums/                # Type-safe enums (PaymentGatewayTypes)
├── Http/
│   ├── Controllers/      # API controllers
│   ├── Requests/         # Form validation rules
│   └── Resources/        # API resource transformers
├── Models/               # Eloquent models (Order, Payment, User)
├── Policies/             # Authorization policies
└── Services/             # Business services (OrderService, PaymentService)
config/                   # Configuration files
database/
├── factories/            # Model factories  
├── migrations/           # Database migrations
├── seeders/              # Data seeders
routes/                   # API route definitions
tests/                    # Feature and unit tests
```

## 🗄️ Database Schema

### Orders Table
```sql
id              INT (PK)
user_id         INT (FK → users)
order_number    VARCHAR(255) UNIQUE
status          ENUM('pending', 'confirmed', 'cancelled')
total_amount    DECIMAL(10,2)
items           JSON
notes           TEXT NULLABLE
timestamps
```

### Payments Table
```sql
id              INT (PK)
payment_id      VARCHAR(255) UNIQUE
order_id        INT (FK → orders)
status          ENUM('pending', 'successful', 'failed')
method          VARCHAR(50)  -- payment gateway type
amount          DECIMAL(10,2)
gateway_response JSON NULLABLE
gateway_metadata JSON NULLABLE
timestamps
```

## 🐛 Troubleshooting

### Common Issues

1. **"Payment gateway not supported" error**
    - Check `config/payment.php` enabled gateways
    - Verify gateway is in `PaymentGatewayTypes` enum

2. **"Order not found" error**
    - Ensure order belongs to authenticated user
    - Check order ID exists in database

3. **"Cannot process payment" error**
    - Verify order status is 'confirmed'
    - Check no successful payment exists for order

4. **Validation errors**
    - Review required fields for selected gateway
    - Check data format matches validation rules

### Debug Mode
Enable debug in `.env`:
```ini
APP_DEBUG=true
APP_ENV=local
```

Check logs:
```bash
tail -f storage/logs/laravel.log
```

## 🚀 Deployment

1. **Set production environment**:
```bash
APP_ENV=production
APP_DEBUG=false
```

2. **Optimize for production**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📜 License

This project is for assessment purposes.

## 📧 Contact

Project Maintainer - Nagwa Ali (nnnnali123@gmail.com)

---

**Need Help?** Check the full API documentation in `swagger.yaml` or create an issue in the repository.

Happy Coding! 💻✨

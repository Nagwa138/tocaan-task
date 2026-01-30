# 📦 Stock Management System (Laravel)

A Laravel-based stock management system with low stock alerts, transfer tracking, and inventory monitoring.

## 🚀 Getting Started

### Prerequisites

- PHP 8.1+
- Composer 2.0+
- MySQL 5.7+ or PostgreSQL 10+
- Node.js 16+ (for frontend assets)

### Installation

1. **Clone the repository**:
```bash
https://github.com/Nagwa138/daftra.git
cd daftra
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
DB_DATABASE=stock_management
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations and seeders**:
```bash
php artisan migrate --seed
```

6.  **Start development server**:
```bash
php artisan serve
```

## 🛠 Development

### Running Tests
```bash
php artisan test
```

### Environment Variables
| Variable                | Description                          | Required |
|-------------------------|--------------------------------------|----------|
| `APP_ENV`               | Application environment              | Yes      |
| `QUEUE_CONNECTION`      | Queue driver (sync/database/redis)   | Yes      |
| `LOW_STOCK_THRESHOLD`   | Default low stock threshold          | No       |

## 🌟 Features
- Automated low stock alerts
- Stock transfer tracking between warehouses
- Inventory reporting

## 📂 Project Structure
```
app/
├── Architecture/      # Application structure (Repositories - Services)
├── Events/            # Application events
├── Listeners/         # Event listeners
├── Models/            # Eloquent models
├── Services/          # Business logic services
config/                # Configuration files
database/
├── factories/         # Model factories  
├── migrations/        # Database migrations
├── seeders/           # Data seeders
tests/                 # Feature and unit tests
```

## 📧 Contact
Project Maintainer - [Nagwa Ali] (nnnnali123@gmail.com)

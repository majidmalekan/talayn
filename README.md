# Gold Trading Platform API

A scalable and secure gold trading API built with Laravel 11, supporting dynamic buy/sell order matching, wallet management, tiered commission, and modular architecture using the Repository and Service patterns. Designed to run in a Dockerized environment using Laravel Sail.

---

## 🚀 Features

* User-based gold trading (buy/sell requests)
* Automatic order matching with price and quantity logic
* Tiered commission calculation with min/max caps
* Wallet system with rial and gold balances
* Atomic (ACID) transactions using `DB::transaction` and `lockForUpdate()`
* Clean architecture with **Repository & Service Layers**
* Factory & Seeder support for testing
* using queue and job for ACID and race condition safe implementation
* Sanctum-based API token authentication
* Runs with Laravel Sail (Docker)

---

## 🧰 Tech Stack

* Laravel 11
* PHP 8.3
* Laravel Sail (Docker)
* MySQL
* Redis (for queue and cache)
* Sanctum (API authentication)

---

## ⚙️ Getting Started with Laravel Sail

### 1. Clone the repository

```bash
git clone https://github.com/majidmalekan/talayn.git
cd talayn
```

### 2. Install dependencies

```bash
composer install
```

### 3. Start Sail containers

```bash
./vendor/bin/sail up -d
```

### 4. Create `.env` and generate key

```bash
cp .env.example .env
./vendor/bin/sail artisan key:generate
```

Make sure to set:

```
DB_HOST=mysql
QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

### 5. Run migrations and seeders

```bash
./vendor/bin/sail artisan migrate --seed
```

---

### 6. Run queue and job

```bash
./vendor/bin/sail artisan queue:work
```

---

## 💼 Commission Rules

| Amount (grams) | Rate |
| -------------- | ---- |
| 0 - 1 g        | 2%   |
| 1 - 10 g       | 1.5% |
| 10+ g          | 1%   |

* Minimum: 500,000 Rials
* Maximum: 5,000,000 Rials

---

## 🔐 Authentication (Sanctum)

* `POST /api/login`

Send token in headers:

```http
Authorization: Bearer {token}
```

---

## 📡 API Endpoints

### Gold Requests

| Method | Endpoint                     | Description                  |
|--------|------------------------------|------------------------------|
| POST   | `/api/v1/gold-requests`      | Create buy/sell gold request |
| GET    | `/api/v1/gold-requests`      | List my gold requests        |
| GET    | `/api/v1/gold-requests/{id}` | Show a gold request          | 
| PUT    | `/api/v1/gold-requests/{id}` |   Update a gold request      | 
| DELETE | `/api/v1/gold-requests/{id}` | Cancel a request             |

### Trades

| Method | Endpoint              | Description        |
|--------|-----------------------|--------------------|
| GET    | `/api/v1/trades`      | List user's trades |
| GET    | `/api/v1/trades/{id}` | View trade details |

---

## 🧱 Architecture

* **Repository Pattern**: for database abstraction
* **Service Layer**
* **Factories/Seeders**: test data via `UserFactory`, `GoldRequestFactory`

---

## 🧪 Testing & Development

### Run Tests

```bash
./vendor/bin/sail artisan test
```

### Seed Sample Data

```php
User::factory()->count(3)->hasWallet()->create();
GoldRequest::factory()->count(10)->create();
```

---

## 📄 License

MIT License — Free for personal and commercial use.

---

## ✨ Author

Developed with ❤️ by \[Your Name].

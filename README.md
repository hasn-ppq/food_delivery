# Food Delivery App 🍕🚚

A comprehensive Laravel-based food delivery platform with multi-role user support, real-time notifications, location-aware delivery, and Filament-powered admin panels.

## Table of Contents

1. [Overview](#overview)
2. [Key Features](#key-features)
3. [Tech Stack](#tech-stack)
4. [Installation](#installation)
5. [Configuration](#configuration)
6. [Usage](#usage)
7. [API Endpoints](#api-endpoints)
8. [Contributing](#contributing)
9. [License](#license)

## Overview

This project is a food delivery backend that supports customers, restaurants, drivers, and administrators. It includes:

- Role-based access control
- OTP authentication
- Restaurant menus and orders

- Order lifecycle management with status updates
- FCM push notifications and background job handling

## Key Features

### User Management

- Multi-role users: Customer, Restaurant Owner, Driver, Admin
- OTP-based login and verification
- Device token registration for push notifications

### Restaurant & Menu

- Restaurant profiles with locations and delivery settings
- Meal management with pricing, discounts, and media
- Restaurant menu browsing and order placement

### Order Processing

- Full order lifecycle: pending → cooking → ready → on the way → delivered
- Automatic cancellation for stale pending orders
- Order details, status updates, and customer tracking

### Notifications & Real-Time

- Firebase Cloud Messaging (FCM) notifications
- Order status notifications for relevant users
- Driver assignment based on proximity to the restaurant
- Event-driven notifications using Laravel events and listeners

### Admin Experience

- Filament-powered admin area
- Separate admin, restaurant, and driver dashboards
- Order and user management from the admin UI

## Tech Stack

- Backend: Laravel 12
- PHP: 8.2+
- Frontend tools: Vite, Node.js
- Authentication/API: Laravel Sanctum
- Notifications: Firebase Cloud Messaging
- Real-time: Laravel Reverb
- Admin UI: Filament 4

## Installation

```bash
git clone https://github.com/hasn-ppq/food_delivery.git
cd food_delivery
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

## Configuration

1. Copy `.env.example` to `.env`.
2. Set database credentials.
3. Configure FCM and Firebase credentials.
4. Set mail and queue driver settings if needed.

## Usage

### Web Interfaces

- Admin panel: `/admin`
- Restaurant panel: `/restaurant`
- Driver panel: `/driver`

### Development commands

```bash
npm run dev
php artisan optimize:clear
php artisan queue:work
```

## API Endpoints

### Authentication

- `POST /api/auth/otp/request` — Request OTP
- `POST /api/auth/otp/verify` — Verify OTP and login
- `POST /api/device-token` — Register device token

### Orders

- `POST /api/orders` — Create order
- `GET /api/my-orders` — List active orders
- `GET /api/orders/{id}` — Order details
- `POST /api/orders/{id}/cancel` — Cancel order

### Restaurants

- `GET /api/restaurants` — List restaurants
- `GET /api/restaurants/{id}/meals` — Restaurant meals


## Contributing

1. Fork the repository
2. Create a new branch
3. Commit your changes
4. Push the branch
5. Open a pull request

## License

This project is licensed under the MIT License.

---

Built with ❤️ using Laravel and Filament

 Admin panel:
 
<img width="1592" height="810" alt="image" src="https://github.com/user-attachments/assets/61bd9134-c27b-4d79-b9bd-0850ec4c490f" />

 Restaurant panel:
 
<img width="1588" height="858" alt="image" src="https://github.com/user-attachments/assets/437afbaa-b804-4586-8a86-778f53842873" />

Driver panel:

<img width="1591" height="650" alt="image" src="https://github.com/user-attachments/assets/c814e65d-e148-4049-8625-455d84421587" />



## 📬 Contact

- GitHub: https://github.com/hasn-ppq  
- Email: husseinahmedkhishn@gmail.com  
- LinkedIn: https://www.linkedin.com/in/hussein-ahmed-khishn/

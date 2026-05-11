# Farmers Marketplace System - Laravel 12

A complete Laravel 12 web application for a Farmers Marketplace System that connects farmers directly with consumers for fresh, quality agricultural products.

## Features

### Authentication & Authorization
- User registration with role selection (Farmer or Consumer)
- Secure login system
- Role-based access control via middleware
- Profile management with picture upload
- Password change functionality

### For Farmers
- **Product Management**: Create, read, update, delete products
- **Product Information**: Name, description, category, price, quantity, image
- **Stock Management**: Update product quantities
- **Dashboard**: View total products, sales, pending orders, recent orders
- **Order Management**: View all orders from consumers, update order status

### For Consumers
- **Product Browsing**: View all available products with search and filter
- **Shopping Cart**: Add/remove items, update quantities, automatic price calculation
- **Checkout**: Place orders with automatic order creation
- **Order Tracking**: View order history and status updates
- **Dashboard**: View cart items, recent orders, recommended products

### Additional Features
- **Settings**: Dark mode toggle, notification preferences
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Tailwind CSS**: Modern, clean UI with dark mode support
- **Database Relationships**: Proper Eloquent relationships between models
- **Validation**: Form validation on both frontend and backend
- **Authorization Policies**: Secure access control with policies

## System Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js 16+ (for Vite)
- npm or yarn

## Installation Guide

### Step 1: Clone or Setup Project

If you haven't already, you should have the project at `c:\Users\User\Documents\myproject`

### Step 2: Install PHP Dependencies

```bash
cd c:\Users\User\Documents\myproject
composer install
```

### Step 3: Copy Environment File

```bash
copy .env.example .env
```

Or on PowerShell:
```powershell
Copy-Item .env.example .env
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Configure Database

Edit `.env` file and update the database configuration:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farmers_marketplace
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 6: Create Database

Create a new MySQL database named `farmers_marketplace`:

```sql
CREATE DATABASE farmers_marketplace;
```

### Step 7: Run Migrations

```bash
php artisan migrate
```

This will create all necessary tables for users, products, carts, orders, etc.

### Step 8: Install Node Dependencies

```bash
npm install
```

### Step 9: Build Assets with Vite

For development (watch mode):
```bash
npm run dev
```

For production (single build):
```bash
npm run build
```

### Step 10: Run Database Seeders

Fill your database with sample data:

```bash
php artisan db:seed
```

This will create:
- 1 test farmer account (farmer@example.com / password)
- 1 test consumer account (consumer@example.com / password)
- 5 additional farmers with 8 products each
- 10 consumers with sample carts
- 20 sample orders

### Step 11: Create Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link for uploaded files (profile pictures and product images).

### Step 12: Start Development Server

In a new terminal, run:

```bash
php artisan serve
```

Or for custom host/port:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 13: Access Application

Open your browser and navigate to:
```
http://localhost:8000
```

Or if running on a different port:
```
http://localhost:8001
http://127.0.0.1:8000
```

## Test Accounts

### Farmer Account
- **Email**: farmer@example.com
- **Password**: password

### Consumer Account
- **Email**: consumer@example.com
- **Password**: password

Plus 5 additional test farmers and 10 additional test consumers created by the seeder.

## Database Schema

### Users Table
- id, name, email, password, role (farmer/consumer)
- phone, address, profile_picture
- dark_mode, notification_enabled
- email_verified_at, remember_token, timestamps

### Products Table
- id, user_id (farmer), name, description, category
- price, quantity, image
- timestamps

### Carts Table
- id, user_id (consumer), subtotal, total
- timestamps

### Cart Items Table
- id, cart_id, product_id, quantity, price, subtotal
- timestamps

### Orders Table
- id, user_id (consumer), subtotal, total
- status (pending/accepted/completed/cancelled)
- notes, timestamps

### Order Items Table
- id, order_id, product_id, farmer_id, quantity
- price, subtotal, timestamps

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/AuthController.php
│   │   ├── CartController.php
│   │   ├── DashboardController.php
│   │   ├── HomeController.php
│   │   ├── OrderController.php
│   │   ├── ProductController.php
│   │   ├── ProfileController.php
│   │   └── SettingsController.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Cart.php
│   ├── CartItem.php
│   ├── Order.php
│   └── OrderItem.php
└── Policies/
    ├── ProductPolicy.php
    └── CartItemPolicy.php

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 0001_01_01_000003_update_users_table.php
│   ├── 0001_01_01_000004_create_products_table.php
│   ├── 0001_01_01_000005_create_carts_table.php
│   ├── 0001_01_01_000006_create_cart_items_table.php
│   ├── 0001_01_01_000007_create_orders_table.php
│   └── 0001_01_01_000008_create_order_items_table.php
├── factories/
│   ├── UserFactory.php
│   ├── ProductFactory.php
│   ├── CartFactory.php
│   ├── CartItemFactory.php
│   ├── OrderFactory.php
│   └── OrderItemFactory.php
└── seeders/
    └── DatabaseSeeder.php

resources/
└── views/
    ├── layouts/app.blade.php
    ├── home.blade.php
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── dashboard/
    │   ├── farmer.blade.php
    │   └── consumer.blade.php
    ├── profile/
    │   ├── edit.blade.php
    │   └── change-password.blade.php
    ├── settings/
    │   └── index.blade.php
    ├── products/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   ├── show.blade.php
    │   └── farmer-list.blade.php
    ├── cart/
    │   └── index.blade.php
    └── orders/
        ├── index.blade.php
        ├── show.blade.php
        └── checkout.blade.php

routes/
└── web.php
```

## Key Artisan Commands

### Database Commands
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset database (drops all tables)
php artisan migrate:reset

# Refresh database (rollback and re-run)
php artisan migrate:refresh

# Seed database
php artisan db:seed

# Refresh and seed
php artisan migrate:refresh --seed
```

### Make Commands
```bash
# Create a new migration
php artisan make:migration create_table_name

# Create a new model
php artisan make:model ModelName

# Create a new controller
php artisan make:controller ControllerName

# Create a new factory
php artisan make:factory ModelNameFactory

# Create a new policy
php artisan make:policy ModelNamePolicy
```

### Development Commands
```bash
# Clear all caches
php artisan cache:clear

# Clear application cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Optimize application
php artisan optimize
```

## Usage Guide

### For Farmers

1. **Register**: Sign up with role "Farmer"
2. **Add Products**: Go to "My Products" → "Add Product", fill in details and upload image
3. **View Orders**: Check "Orders" to see customer orders
4. **Update Status**: Click on order and change status (Pending → Accepted → Completed)
5. **Manage Products**: Edit or delete products as needed

### For Consumers

1. **Register**: Sign up with role "Consumer"
2. **Browse Products**: Click "Browse Products" to see available items
3. **Search/Filter**: Use search box and category filter to find products
4. **Add to Cart**: Click "Add to Cart" on individual product pages
5. **Manage Cart**: View and update quantities in the cart
6. **Checkout**: Proceed to checkout to place order
7. **Track Orders**: View order status in "Orders" section

## Common Issues & Troubleshooting

### Port Already in Use
If port 8000 is already in use, use a different port:
```bash
php artisan serve --port=8001
```

### Database Connection Error
- Verify MySQL is running
- Check DB credentials in `.env`
- Ensure database exists

### File Upload Issues
- Verify storage link is created: `php artisan storage:link`
- Check permissions on `storage` and `bootstrap/cache` directories
- Ensure `php_fileinfo` extension is enabled

### Migrations Not Running
```bash
# Force fresh migration
php artisan migrate:refresh

# Migrate with seeding
php artisan migrate:refresh --seed
```

### Assets Not Loading
```bash
# Rebuild Vite assets
npm run build
```

### Role-based Access Denied
- Verify user role in database
- Check middleware in `routes/web.php`
- Ensure user assigned correct role during registration

## API Reference

### Authentication Routes
- `GET /register` - Registration form
- `POST /register` - Create account
- `GET /login` - Login form
- `POST /login` - Authenticate user
- `POST /logout` - Logout (authenticated)

### Public Routes
- `GET /` - Home page
- `GET /products` - Browse products
- `GET /products/{product}` - View product details

### Authenticated Routes (Consumer & Farmer)
- `GET /dashboard` - Dashboard
- `GET /profile/edit` - Edit profile
- `PUT /profile` - Update profile
- `GET /profile/change-password` - Change password form
- `PUT /profile/password` - Update password
- `GET /settings` - Settings page
- `PUT /settings` - Update settings
- `GET /orders` - View orders
- `GET /orders/{order}` - View order details

### Consumer Routes
- `GET /cart` - View cart
- `POST /cart/{product}` - Add to cart
- `PUT /cart-item/{cartItem}` - Update quantity
- `DELETE /cart-item/{cartItem}` - Remove from cart
- `DELETE /cart/clear` - Clear cart
- `GET /checkout` - Checkout page
- `POST /checkout` - Place order

### Farmer Routes
- `GET /farmer/products` - My products
- `GET /farmer/products/create` - Add product form
- `POST /farmer/products` - Create product
- `GET /farmer/products/{product}/edit` - Edit product form
- `PUT /farmer/products/{product}` - Update product
- `DELETE /farmer/products/{product}` - Delete product
- `PUT /orders/{order}/status` - Update order status

## Performance Optimization

### For Production:
```bash
# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Build assets
npm run build
```

### Caching:
```bash
php artisan cache:clear
php artisan config:cache
php artisan view:cache
```

## Security Best Practices

1. **Environment Variables**: Never commit `.env` file
2. **HTTPS**: Use HTTPS in production
3. **CORS**: Configure CORS if needed
4. **Rate Limiting**: Implement rate limiting for APIs
5. **SQL Injection**: Always use parameterized queries (Eloquent)
6. **XSS Protection**: Laravel includes CSRF protection by default

## Support & Troubleshooting

If you encounter issues:

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Clear all caches**: `php artisan cache:clear && php artisan config:clear`
3. **Verify database connection**: Check `.env` and MySQL running
4. **Check migrations**: `php artisan migrate:status`
5. **Verify storage link**: `php artisan storage:link`

## License

This project is beginner-friendly and designed for learning Laravel 12.

## Contributors

Created as a complete Laravel 12 marketplace system with:
- Full authentication system
- Role-based access control
- Complete e-commerce functionality
- Responsive design with Tailwind CSS
- Modern development practices

---

**Happy farming! 🌾**

# 🌾 Farmers Marketplace System - Complete Laravel 12 Application

A production-ready Laravel 12 web application for connecting local farmers directly with consumers. Built with Laravel 12, MySQL, Tailwind CSS, and Vite.

## 🎯 Project Overview

This is a complete **farmers marketplace system** that enables:
- **Farmers** to list and sell their products directly to consumers
- **Consumers** to browse, purchase, and track orders from local farmers
- **Direct connections** between farmers and consumers without intermediaries
- **Real-time order management** with status tracking
- **Responsive design** working on all devices

## ✨ Key Features Implemented

### ✅ Authentication System
- Registration with role selection (Farmer/Consumer)
- Secure login with remember me functionality
- Password reset/change capability
- Session management

### ✅ Farmer Features
- **Dashboard**: Total products, sales, pending orders, recent activity
- **Product Management**: Full CRUD operations
  - Add products with name, description, category, price, quantity
  - Upload product images
  - Edit stock quantities
  - Delete products
- **Order Management**: View all customer orders
  - See ordered products and quantities
  - Customer information and delivery address
  - Update order status (Pending → Accepted → Completed)

### ✅ Consumer Features
- **Dashboard**: Cart items count, recent orders, recommended products
- **Product Catalog**: 
  - Browse all available products
  - Search by product name/description
  - Filter by category
  - View detailed product information
- **Shopping Cart**:
  - Add/remove items
  - Update quantities
  - Automatic price calculation
  - Clear entire cart
- **Checkout & Orders**:
  - Checkout form with delivery info
  - Order confirmation
  - Order history tracking
  - Order status updates

### ✅ User Profile Management
- Edit profile information (name, email, phone, address)
- Upload profile picture
- Change password with current password verification
- View profile in sidebar

### ✅ Settings
- Dark mode toggle
- Notification preferences
- Account type display

### ✅ Technical Features
- Role-based access control (RBAC) middleware
- Eloquent ORM with proper relationships
- Form validation with detailed error messages
- Authorization policies for secure resource access
- Responsive Tailwind CSS design with dark mode
- Modern MVC architecture
- Database migrations for schema versioning
- Factory and seeder classes for testing

## 📁 Complete File Structure

### Controllers (8 controllers)
```
app/Http/Controllers/
├── Auth/AuthController.php         - Authentication logic
├── CartController.php              - Shopping cart management
├── DashboardController.php         - Dashboard for farmers/consumers
├── HomeController.php              - Home page controller
├── OrderController.php             - Order management
├── ProductController.php           - Product CRUD & browsing
├── ProfileController.php           - User profile management
└── SettingsController.php          - User settings
```

### Models (6 models with relationships)
```
app/Models/
├── User.php                        - User with farmer/consumer roles
├── Product.php                     - Products with farmer relationship
├── Cart.php                        - Shopping carts
├── CartItem.php                    - Items in carts
├── Order.php                       - Customer orders
└── OrderItem.php                   - Items in orders
```

### Middleware (1 role middleware)
```
app/Http/Middleware/
└── CheckRole.php                   - Role-based access control
```

### Policies (2 authorization policies)
```
app/Policies/
├── ProductPolicy.php               - Product ownership check
└── CartItemPolicy.php              - Cart item ownership check
```

### Database (8 migrations)
```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 0001_01_01_000003_update_users_table.php      - Add role, phone, address, etc
├── 0001_01_01_000004_create_products_table.php
├── 0001_01_01_000005_create_carts_table.php
├── 0001_01_01_000006_create_cart_items_table.php
├── 0001_01_01_000007_create_orders_table.php
└── 0001_01_01_000008_create_order_items_table.php
```

### Factories (6 factories)
```
database/factories/
├── UserFactory.php                 - with farmer/consumer states
├── ProductFactory.php
├── CartFactory.php
├── CartItemFactory.php
├── OrderFactory.php
└── OrderItemFactory.php
```

### Seeders (1 seeder)
```
database/seeders/
└── DatabaseSeeder.php              - Creates test data (farmers, consumers, products, orders)
```

### Views (17 Blade templates)
```
resources/views/
├── layouts/app.blade.php           - Main layout with sidebar
├── home.blade.php                  - Homepage
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
│   ├── index.blade.php             - Browse products
│   ├── create.blade.php            - Add product
│   ├── edit.blade.php              - Edit product
│   ├── show.blade.php              - Product details
│   └── farmer-list.blade.php       - My products list
├── cart/
│   └── index.blade.php             - Shopping cart
└── orders/
    ├── index.blade.php             - Order listing
    ├── show.blade.php              - Order details
    └── checkout.blade.php          - Checkout form
```

### Routes
```
routes/web.php
- Public routes: home, products
- Auth routes: login, register, logout
- Protected routes: dashboard, profile, settings, cart, orders
- Role-based routes: farmer products, order status updates
```

## 🚀 Quick Start Guide

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js 16+

### Installation (5 minutes)
```bash
# 1. Clone/enter project directory
cd c:\Users\User\Documents\myproject

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
copy .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_DATABASE=farmers_marketplace
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Create database
# In MySQL: CREATE DATABASE farmers_marketplace;

# 6. Run migrations & seed
php artisan migrate
php artisan db:seed

# 7. Link storage for uploads
php artisan storage:link

# 8. Build assets
npm run dev  # for development
# or
npm run build # for production

# 9. Start server (in new terminal)
php artisan serve
```

### Access Application
- URL: `http://localhost:8000`
- Farmer: farmer@example.com / password
- Consumer: consumer@example.com / password

## 📊 Database Schema

### Users
- id, name, email, password, email_verified_at, remember_token
- **role**: farmer | consumer
- **phone**: optional contact number
- **address**: optional delivery address
- **profile_picture**: optional uploaded image
- **dark_mode**: boolean preference
- **notification_enabled**: boolean preference
- timestamps

### Products
- id, user_id (farmer), name, description, category, price, quantity, image
- timestamps

### Carts
- id, user_id (consumer), subtotal, total
- timestamps

### Cart Items
- id, cart_id, product_id, quantity, price, subtotal
- timestamps

### Orders
- id, user_id (consumer), subtotal, total, **status** (pending/accepted/completed/cancelled), notes
- timestamps

### Order Items
- id, order_id, product_id, farmer_id, quantity, price, subtotal
- timestamps

## 🔐 Security Features

✅ Role-based access control (RBAC)
✅ Authorization policies for resource ownership
✅ CSRF token protection
✅ Password hashing with bcrypt
✅ Authenticated session management
✅ Form validation on backend
✅ Eloquent ORM prevents SQL injection
✅ File upload security

## 🎨 UI/UX Features

✅ Responsive Tailwind CSS design
✅ Dark mode support
✅ Sidebar navigation for authenticated users
✅ Flash messages for user feedback
✅ Form validation error display
✅ Mobile-friendly interface
✅ Smooth transitions and hover effects
✅ Clear status indicators with colors

## 📈 Sample Data

Database seeder creates:
- **1** test farmer account with complete profile
- **1** test consumer account with complete profile
- **5** additional farmers with 8 products each (40 products)
- **10** consumers with sample carts (2-5 items per cart)
- **20** sample orders with various statuses

## 🛠️ Development Commands

```bash
# Create new migration
php artisan make:migration migration_name

# Create new model
php artisan make:model ModelName

# Create new controller
php artisan make:controller ControllerName

# Create factory
php artisan make:factory ModelNameFactory

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh database with seed
php artisan migrate:refresh --seed

# Cache clear
php artisan cache:clear
```

## 🔧 Environment Variables

Key `.env` variables to configure:
```
APP_NAME=FarmersMarketplace
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farmers_marketplace
DB_USERNAME=root
DB_PASSWORD=password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

## 📋 Routes Summary

| Method | Route | Purpose | Auth | Role |
|--------|-------|---------|------|------|
| GET | / | Home | No | - |
| GET | /products | Browse products | No | - |
| GET/POST | /login | Login | No | - |
| GET/POST | /register | Register | No | - |
| POST | /logout | Logout | Yes | - |
| GET | /dashboard | Dashboard | Yes | - |
| GET/PUT | /profile | Edit profile | Yes | - |
| GET/PUT | /settings | Settings | Yes | - |
| GET | /cart | View cart | Yes | Consumer |
| POST | /cart/{product} | Add to cart | Yes | Consumer |
| GET/POST | /checkout | Checkout | Yes | Consumer |
| GET | /orders | Orders list | Yes | - |
| GET | /orders/{id} | Order details | Yes | - |
| GET/POST | /farmer/products | Farmer products | Yes | Farmer |
| PUT | /farmer/products/{id} | Update product | Yes | Farmer |
| DELETE | /farmer/products/{id} | Delete product | Yes | Farmer |

## 📚 Documentation Files

- **SETUP_GUIDE.md** - Detailed installation and troubleshooting
- **README.md** - This file
- **Code Comments** - Inline documentation in all classes

## 🎯 Next Steps / Future Enhancements

Possible enhancements:
- Payment integration (Stripe, PayPal)
- Email notifications
- Product reviews and ratings
- Wishlist feature
- Admin panel
- Order tracking with maps
- API endpoints (JSON endpoints)
- Two-factor authentication
- Bulk product upload
- Advanced analytics/reporting

## 💡 Learning Points

This project demonstrates:
- Laravel MVC architecture
- Eloquent ORM relationships
- Authentication & authorization
- Validation and error handling
- Database migrations
- Blade templating
- Tailwind CSS styling
- Role-based access control
- Factory and Seeder patterns
- Policy-based authorization
- Form handling and file uploads

## 🐛 Troubleshooting

**Issue**: Port 8000 already in use
```bash
php artisan serve --port=8001
```

**Issue**: Database connection failed
- Check MySQL is running
- Verify `.env` credentials
- Ensure database exists

**Issue**: Migrations fail
```bash
php artisan migrate:refresh --seed
```

**Issue**: File uploads not working
```bash
php artisan storage:link
```

**Issue**: Assets not loading
```bash
npm run build
```

## 📞 Support

For detailed help, refer to:
- SETUP_GUIDE.md for installation
- Laravel documentation: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com
- Eloquent ORM: https://laravel.com/docs/eloquent

## ✅ Completeness Checklist

- ✅ Authentication (Login/Register)
- ✅ Role-based access (Farmer/Consumer)
- ✅ Profile management with picture upload
- ✅ Settings (dark mode, notifications)
- ✅ Product CRUD (Farmer)
- ✅ Product browsing with search/filter (Consumer)
- ✅ Shopping cart with calculations
- ✅ Checkout system
- ✅ Orders management
- ✅ Order status tracking
- ✅ Dashboard for both roles
- ✅ Responsive design
- ✅ Dark mode support
- ✅ Database migrations
- ✅ Models with relationships
- ✅ Controllers with logic
- ✅ Routes configured
- ✅ Blade templates
- ✅ Middleware for roles
- ✅ Factories and seeders
- ✅ Validation
- ✅ Authorization policies
- ✅ Error handling
- ✅ Setup documentation

## 🎓 Educational Value

This complete application is perfect for learning:
- Full-stack Laravel development
- E-commerce system design
- Role-based access control
- Database relationships
- Form handling and validation
- Modern UI design with Tailwind CSS
- Production-ready code patterns

---

**Built with ❤️ using Laravel 12, MySQL, Tailwind CSS, and Vite**

**Ready to deploy! 🚀**

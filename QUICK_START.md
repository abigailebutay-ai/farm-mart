# ⚡ Quick Start - Farmers Marketplace System

## One-Time Setup (First Time Only)

```bash
cd c:\Users\User\Documents\myproject

# 1. Install PHP dependencies
composer install

# 2. Create .env file
copy .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Install Node dependencies
npm install
```

## Database Setup

```sql
-- Create database in MySQL
CREATE DATABASE farmers_marketplace;
```

Then update `.env`:
```
DB_DATABASE=farmers_marketplace
DB_USERNAME=root
DB_PASSWORD=your_password
```

## Run These Commands Once

```bash
# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed

# Create storage link for uploads
php artisan storage:link
```

## Every Development Session

```bash
# Terminal 1: Start Vite (asset bundler)
npm run dev

# Terminal 2: Start Laravel server
php artisan serve
```

Then open: **http://localhost:8000**

## Test Credentials

| Role | Email | Password |
|------|-------|----------|
| Farmer | farmer@example.com | password |
| Consumer | consumer@example.com | password |

## Key Files to Know

- **Routes**: `routes/web.php` - All URL mappings
- **Controllers**: `app/Http/Controllers/` - Business logic
- **Models**: `app/Models/` - Database objects
- **Views**: `resources/views/` - HTML templates
- **Database**: `database/migrations/` - Schema
- **Config**: `.env` - Environment variables

## Common Commands

```bash
# Refresh database (delete all + reset)
php artisan migrate:refresh --seed

# View database status
php artisan migrate:status

# Clear all caches
php artisan cache:clear

# Make a new migration
php artisan make:migration table_name

# Make a new model
php artisan make:model ModelName

# Make a new controller
php artisan make:controller ControllerName
```

## If Something Breaks

1. **Database issues**: 
   ```bash
   php artisan migrate:refresh --seed
   ```

2. **Cache issues**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

3. **Asset issues**:
   ```bash
   npm run build
   ```

4. **Port already in use**:
   ```bash
   php artisan serve --port=8001
   ```

## Project Features To Try

### As a Farmer (farmer@example.com):
1. Login to dashboard
2. Go to "My Products"
3. Add a new product
4. Check "Orders" to see customer orders
5. Update order statuses

### As a Consumer (consumer@example.com):
1. Login to dashboard
2. Go to "Browse Products"
3. Search or filter products
4. Add items to cart
5. Go through checkout
6. Track your orders

## File Upload Paths

- **Profile pictures**: `storage/app/public/profile-pictures/`
- **Product images**: `storage/app/public/products/`

Access via: `http://localhost:8000/storage/profile-pictures/filename.jpg`

## Database Tables

| Table | Purpose |
|-------|---------|
| users | All users (farmers & consumers) |
| products | Farmer's products |
| carts | Consumer shopping carts |
| cart_items | Items in carts |
| orders | Customer orders |
| order_items | Items in orders |

## URLs Quick Reference

| Page | URL |
|------|-----|
| Home | http://localhost:8000 |
| Login | http://localhost:8000/login |
| Register | http://localhost:8000/register |
| Dashboard | http://localhost:8000/dashboard |
| Browse Products | http://localhost:8000/products |
| My Cart | http://localhost:8000/cart |
| My Orders | http://localhost:8000/orders |
| My Profile | http://localhost:8000/profile/edit |
| Settings | http://localhost:8000/settings |
| Farmer Products | http://localhost:8000/farmer/products |

## Development Tips

1. **Use browser DevTools** (F12) to debug frontend
2. **Check Laravel logs**: `storage/logs/laravel.log`
3. **Use `dd()` function** to debug in code: `dd($variable);`
4. **Use `@dump()`** in Blade templates for debugging
5. **Check database** with MySQL tool to verify data

## Dependencies

- **Laravel 12** - PHP Framework
- **Tailwind CSS** - Styling
- **Vite** - Asset bundler
- **MySQL** - Database
- **Eloquent** - ORM

## Notes

- Passwords are hashed with bcrypt
- All timestamps are in UTC
- Images stored in `storage/app/public/`
- Database relations properly set up with foreign keys
- Role-based access control implemented

---

## Ready to Code? 

Start with understanding:
1. `routes/web.php` - How URLs work
2. `app/Models/` - How data is structured
3. `app/Http/Controllers/` - How requests are handled
4. `resources/views/` - How pages are rendered

Happy coding! 🌾👨‍💻

# ShareStrength TALL Stack - Quick Reference Guide

## 🚀 Quick Start

### 1. Start Development Servers
```bash
cd backend

# Terminal 1
php artisan serve

# Terminal 2  
npm run dev
```

### 2. Access Application
- **URL**: http://localhost:8000
- **Login**: http://localhost:8000/login

### 3. Test Credentials
| Role | Email | Password |
|------|-------|----------|
| User (PWD) | user@example.com | password |
| HelpMate | helpmate@example.com | password |
| Admin | admin@example.com | password |

---

## ✅ Working Features

### Authentication
- ✅ Login with role-based redirect
- ✅ User registration
- ✅ HelpMate registration
- ✅ Logout

### Dashboard
- ✅ User dashboard with task list
- ✅ Application review section
- ✅ Quick access sidebar
- ✅ **Post New Task button** (NOW WORKING!)

### Task Management
- ✅ **Post Task form** (FULLY FUNCTIONAL)
  - Title & description
  - Location & budget
  - Urgency level
  - Required skills (multi-select)
  - Scheduled date/time
  - Full validation

---

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

**Current Status**: ✅ **20/20 tests passing (63 assertions)**

### Run Specific Tests
```bash
# Authentication tests
php artisan test tests/Feature/Auth/

# Task tests
php artisan test tests/Feature/Tasks/

# Specific test file
php artisan test tests/Feature/Auth/LoginTest.php
```

---

## 📁 Key Files

### Routes
- `routes/web.php` - All application routes

### Livewire Components
- `app/Livewire/Auth/Login.php` - Login logic
- `app/Livewire/Auth/RegisterUser.php` - User registration
- `app/Livewire/Tasks/PostTask.php` - Task posting
- `app/Livewire/Dashboards/UserDashboard.php` - User dashboard

### Views
- `resources/views/livewire/auth/login.blade.php`
- `resources/views/livewire/tasks/post-task.blade.php`
- `resources/views/livewire/dashboards/user-dashboard.blade.php`

### Tests
- `tests/Feature/Auth/LoginTest.php` - 6 tests
- `tests/Feature/Auth/RegisterUserTest.php` - 4 tests
- `tests/Feature/Tasks/PostTaskTest.php` - 7 tests

---

## 🔧 Common Commands

### Database
```bash
# Fresh migration
php artisan migrate:fresh

# Seed test users
php artisan db:seed --class=TestCredentialsSeeder

# Reset & seed
php artisan migrate:fresh --seed
```

### Cache
```bash
# Clear all caches
php artisan optimize:clear

# Clear config
php artisan config:clear

# Clear views
php artisan view:clear
```

### Development
```bash
# List all routes
php artisan route:list

# Create Livewire component
php artisan make:livewire ComponentName

# Run tests with coverage
php artisan test --coverage
```

---

## 📚 Documentation

1. **`documentation/IMPLEMENTATION_SUMMARY.md`** - Complete feature overview
2. **`documentation/testing/README.md`** - Testing strategy
3. **`documentation/testing/AUTH_TESTS.md`** - Auth test details
4. **`README.md`** - Project setup

---

## 🐛 Troubleshooting

### Issue: White screen on page load
**Solution**: Clear caches
```bash
php artisan optimize:clear
```

### Issue: Login not working
**Solution**: Check database seeding
```bash
php artisan db:seed --class=TestCredentialsSeeder
```

### Issue: Styles not loading
**Solution**: Restart Vite
```bash
# Stop npm run dev (Ctrl+C)
npm run dev
```

### Issue: Tests failing
**Solution**: Fresh test database
```bash
php artisan test --recreate-databases
```

---

## 🎯 Feature Status

| Feature | Status | Tests | Notes |
|---------|--------|-------|-------|
| Landing Page | ✅ Done | - | Fully responsive |
| Login | ✅ Done | 6/6 ✅ | Role-based routing |
| Registration | ✅ Done | 4/4 ✅ | User & HelpMate |
| User Dashboard | ✅ Done | - | Task list, logout |
| Post Task | ✅ Done | 7/7 ✅ | Full validation |
| Task Browsing | 📋 Planned | - | For HelpMates |
| Applications | 🚧 Partial | - | UI ready |
| Payments | 📋 Planned | - | Future |

**Legend**: ✅ Complete | 🚧 In Progress | 📋 Planned

---

## 💡 Tips

1. **Always run tests** before committing code
2. **Use Livewire DevTools** for debugging (browser extension)
3. **Check Laravel logs** at `storage/logs/laravel.log`
4. **Use `dd()` or `dump()`** for debugging in Livewire components
5. **Clear browser cache** if styles don't update

---

**Last Updated**: 2026-01-19  
**Version**: 1.0.0

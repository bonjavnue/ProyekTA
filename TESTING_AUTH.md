# Testing Authentication Setup

## ✅ PRE-REQUISITES

Pastikan sudah:
1. Install dependencies: `composer install`
2. Setup `.env` file dengan database config
3. Generate app key: `php artisan key:generate`
4. Run migrations: `php artisan migrate`
5. Seed database: `php artisan db:seed` (untuk test data)

---

## 🧪 TEST STEPS

### **Step 1: Test User Registration**
1. Buka: `http://localhost:8000/register`
2. Isi form:
   - Email: `testadmin@perusahaan.com`
   - Role: `Admin`
   - Password: `password123`
   - Confirm Password: `password123`
3. Klik Register
4. **Expected:** Redirect ke `/admin/jenispelatihan` dan sudah login

### **Step 2: Test Login dengan Seed Data**
1. Jika sudah run `php artisan db:seed`, bisa langsung test:
   - Email: `admin@perusahaan.com`
   - Password: `password123`
2. Buka: `http://localhost:8000/login`
3. Isi form dengan data di atas
4. Klik Login
5. **Expected:** Redirect ke `/admin/jenispelatihan` dan sudah login

### **Step 3: Test Role-Based Access**
1. Login sebagai Admin → should access:
   - `/admin/jenispelatihan` ✅
   - `/admin/datakaryawan` ✅
   - `/admin/kelolasupervisor` ✅

2. Login sebagai Supervisor (test dengan register role supervisor) → should access:
   - `/admin/jenispelatihan` ✅
   - `/admin/datakaryawan` ❌ (403 Forbidden)
   - `/admin/kelolasupervisor` ❌ (403 Forbidden)

### **Step 4: Test Unauthenticated Access**
1. Logout terlebih dahulu
2. Coba akses `/admin/jenispelatihan`
3. **Expected:** Redirect ke `/login`

### **Step 5: Test Logout**
1. Login dengan akun apapun
2. Buka browser console (F12)
3. Submit form logout (atau cari tombol logout di halaman)
4. **Expected:** Redirect ke `/login` dan session cleared

---

## 📊 TEST USERS (dari seeder)

| Email | Password | Role |
|-------|----------|------|
| admin@perusahaan.com | password123 | Admin |
| supervisor@perusahaan.com | password123 | Supervisor |

---

## 🔍 DEBUG TIPS

### Check Current User
```php
// Di controller atau view
auth()->user() // Get current user
auth()->user()->email // Get user email
auth()->user()->role // Get user role
auth()->check() // Check if authenticated
```

### Check Routes
```bash
php artisan route:list
# Akan show semua routes dengan middleware
```

### Check Logs
```bash
# Linux/Mac
tail -f storage/logs/laravel.log

# Windows
Get-Content storage/logs/laravel.log -Tail 20 -Wait
```

---

## ⚠️ COMMON ISSUES

### Issue: "Column not found: email"
**Solution:** Run migrations
```bash
php artisan migrate
```

### Issue: "No application encryption key has been generated"
**Solution:** Generate key
```bash
php artisan key:generate
```

### Issue: Cannot login even with correct credentials
**Solution:** Check password hashing
```bash
# Test di tinker
php artisan tinker
>>> use Illuminate\Support\Facades\Hash
>>> Hash::check('password123', Hash::make('password123'))
// Should return true
```

### Issue: CSRF Token Mismatch
**Solution:** Make sure form has `@csrf` token
```blade
<form method="POST">
    @csrf
    <!-- form fields -->
</form>
```

---

## 📝 NEXT TEST PHASES

1. **Database Relationships Test**
   - Verify Bagian ↔ User relationship
   - Verify Karyawan ↔ Bagian relationship

2. **JadwalPelatihan CRUD Test** (akan dikerjakan next)
   - Create jadwal pelatihan
   - Update jadwal
   - Delete jadwal
   - Generate QR code

3. **Public Attendance Page Test** (akan dikerjakan selanjutnya)
   - Access tanpa login
   - GPS validation
   - File upload

---

Setelah semua test pass, lanjut ke implementasi JadwalPelatihan Management! 🚀

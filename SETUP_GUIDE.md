# JK PropManager — Setup Guide

---

## Why it works on `php artisan serve` but NOT on XAMPP / cPanel

Laravel needs to know its own URL. When `APP_URL` is wrong, all asset links,
manifest.json, sw.js, and redirect paths break silently.

---

## ✅ XAMPP Setup

### 1. Place files
Copy the whole project folder into:
```
C:\xampp\htdocs\jk\
```

### 2. Set APP_URL in `.env`
```env
APP_URL=http://localhost/jk/public
```

### 3. Make sure mod_rewrite is enabled
Open `C:\xampp\apache\conf\httpd.conf` and check this line is NOT commented out:
```
LoadModule rewrite_module modules/mod_rewrite.so
```
Also find `AllowOverride None` for your htdocs directory and change it to:
```
AllowOverride All
```

### 4. Access the app
Open: `http://localhost/jk/public`

---

## ✅ cPanel (Shared Hosting) Setup

### 1. Upload files
Upload everything EXCEPT the `public/` folder to a subfolder like `propmanager/`
in your home directory (NOT inside `public_html`).

Upload ONLY the **contents** of `public/` into `public_html/` (or a subdomain folder).

### 2. Fix `public/index.php`
Edit `public_html/index.php` and update the paths:
```php
require __DIR__.'/../propmanager/vendor/autoload.php';
$app = require_once __DIR__.'/../propmanager/bootstrap/app.php';
```

### 3. Set APP_URL in `.env`
```env
APP_URL=https://yourdomain.com
```
> For a subdomain like `app.yourdomain.com`:
> ```env
> APP_URL=https://app.yourdomain.com
> ```

### 4. Set correct file permissions
```
storage/         → 775
bootstrap/cache/ → 775
```

### 5. Run these commands (via SSH or cPanel Terminal)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

---

## ✅ PWA Install Button (Mobile)

The sidebar now shows an **Install App** button automatically on mobile:

- **Android (Chrome/Edge):** Tap "Install App" → native install prompt appears
- **iOS (Safari):** Shows "Tap Share → Add to Home Screen" instructions

### If the install button still doesn't appear on Android:
1. Make sure you're on **HTTPS** (required for PWA on Android)
2. Open Chrome DevTools → Application tab → Manifest → check for errors
3. Clear site data and try again: Chrome menu → Settings → Site Settings → Clear

### XAMPP Note:
PWA install requires HTTPS. On XAMPP (HTTP), the install prompt will NOT fire.
Use the manual browser menu instead: tap **⋮ → Add to Home Screen**.

To test PWA locally with HTTPS, use [ngrok](https://ngrok.com):
```bash
ngrok http 80
```
Then open the ngrok `https://` URL on your phone.

---

## Quick .env Reference

| Environment       | APP_URL example                        |
|-------------------|----------------------------------------|
| artisan serve     | `http://localhost:8000`               |
| XAMPP subfolder   | `http://localhost/jk/public`          |
| cPanel domain     | `https://yourdomain.com`              |
| cPanel subdomain  | `https://app.yourdomain.com`          |

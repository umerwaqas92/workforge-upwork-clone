# 🚀 WorkForge Marketplace - Deployment, Sync & Environment Guide

This document is the **standard operating manual** for any AI agent, developer, or team member working on **WorkForge Upwork Clone**. It outlines environment configuration, automated testing, GitHub version control, and 1-click production deployment.

---

## 📌 1. Environment & Server Architecture Overview

| Environment | Purpose | URL / Host | Notes |
| :--- | :--- | :--- | :--- |
| **Local Dev** | Rapid Coding & Testing | `http://127.0.0.1:8008` | SQLite / MySQL local database with Livewire 3 hot-reloading |
| **GitHub** | Version Control & Backups | [`github.com/umerwaqas92/workforge-upwork-clone`](https://github.com/umerwaqas92/workforge-upwork-clone) | Remote branch `main` |
| **Production** | Live Public SaaS | `http://workforgemarketplace.gt.tc/` | InfinityFree Shared Web Node |
| **Production FTP** | Direct File Uploads | `ftpupload.net:21` | User: `if0_42654988`, Pass: `2UwmsMo2RskP`, Root: `/htdocs` |
| **Production DB** | Live MySQL Database | `sql301.infinityfree.com` | User: `if0_42654988`, Pass: `2UwmsMo2RskP`, DB: `if0_42654988_workforgdedge` |

---

## 💻 2. Local Development Workflow

When creating new features or fixing bugs:

1. **Start Local Server**:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8008
   ```
2. **Compile Frontend Assets**:
   ```bash
   npm run build
   ```
3. **Run Automated Test Suite**:
   Always ensure tests pass with 0 failures before deploying:
   ```bash
   php artisan test
   ```

---

## ⚡ 3. One-Click Production Deployment

We created an automated deployment script [`deploy.php`](./deploy.php) that handles compiling assets, running tests, syncing modified files to FTP, and triggering live database migrations in a single command.

### Standard Production Deploy:
```bash
php deploy.php
```

### Quick Deploy (Skip tests for fast hotfixes):
```bash
php deploy.php --quick
```

---

## 📦 4. Manual Sync Procedures (Reference)

### A. Git Version Control Sync:
```bash
git add .
git commit -m "Your descriptive commit message"
git push origin main
```

### B. Uploading Specific Changed Files to Production FTP:
If you edited specific files and want to upload them via PHP FTP:
```php
php -r '
$ftp = ftp_connect("ftpupload.net", 21, 20);
if (ftp_login($ftp, "if0_42654988", "2UwmsMo2RskP")) {
    ftp_pasv($ftp, true);
    ftp_put($ftp, "/htdocs/app/Models/YourFile.php", "app/Models/YourFile.php", FTP_BINARY);
    ftp_put($ftp, "/htdocs/resources/views/your-view.blade.php", "resources/views/your-view.blade.php", FTP_BINARY);
    ftp_close($ftp);
    echo "✓ Upload complete!\n";
}
'
```

### C. Live Database Migrations:
To trigger live database migrations or cron recalculation:
* Visit or curl the live web hook:
  ```bash
  curl http://workforgemarketplace.gt.tc/cron/recalculate-badges
  ```

---

## 🛠️ 5. Key Artisan Commands

| Command | Description |
| :--- | :--- |
| `php artisan test` | Runs the 16-test PHPUnit test suite |
| `php artisan freelancers:recalculate-badges` | Evaluates JSS, earnings, and awards Rising Talent / Top Rated / Top Rated Plus badges |
| `php artisan view:clear && php artisan route:clear` | Clears compiled view/route caches |
| `php artisan migrate` | Runs pending local database migrations |

---

## 👥 6. Demo Test Personas (Quick Switcher)

Use these 1-click accounts on both local and production:
* **Client (Marcus Vance)**: `http://workforgemarketplace.gt.tc/quick-login/client`
* **Freelancer (Alex Reed)**: `http://workforgemarketplace.gt.tc/quick-login/freelancer`
* **Admin Super-Panel**: `http://workforgemarketplace.gt.tc/admin/dashboard` *(Login as Admin via `/quick-login/admin`)*

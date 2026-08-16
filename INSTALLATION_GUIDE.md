# 📘 Complete Beginner's Installation & Setup Guide
### WorkForge – Freelance Marketplace Script (Upwork Clone)

Welcome! This guide is written in simple, non-technical language so that **anyone** can easily install, customize the branding (name, logo, favicon), configure the environment, and launch this script on any web hosting provider (cPanel, Hostinger, Namecheap, Bluehost, SiteGround, VPS, etc.).

> 💡 **Visual guide:** Prefer a clickable, illustrated manual? Open **`documentation/index.html`** in your browser — it includes the same steps with screenshots of the marketplace.

---

## 📑 Table of Contents
1. [What You Need Before Starting](#1-what-you-need-before-starting)
2. [How to Customize Name, Logo & Icons (Before Uploading)](#2-how-to-customize-name-logo--icons)
3. [How to Configure Your Environment (`.env` File)](#3-how-to-configure-your-environment-env-file)
4. [How to Create a Database in Your Hosting](#4-how-to-create-a-database-in-your-hosting)
5. [Uploading the Files to Your Hosting](#5-uploading-the-files-to-your-hosting)
6. [Importing Database & Running Migrations](#6-importing-database--running-migrations)
7. [Setting Up Public Storage for Images](#7-setting-up-public-storage-for-images)
8. [Configuring Email & Payment Gateways](#8-configuring-email--payment-gateways)
9. [Default Admin & Demo Logins](#9-default-admin--demo-logins)
10. [Troubleshooting & Common Questions](#10-troubleshooting--common-questions)
11. [Support & Contact](#11-support--contact)

---

## 1. What You Need Before Starting

Make sure your web hosting provides:
- **PHP Version**: 8.2 or 8.3+
- **PHP Extensions**: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `PDO_MySQL`, `Tokenizer`, `XML`, `GD` *(Standard on almost all cPanel / Hostinger accounts)*
- **MySQL / MariaDB**: Version 5.7+ or 8.0+
- **Domain Name**: e.g., `https://yourdomain.com`

---

## 2. How to Customize Name, Logo & Icons

You can customize your website's name, brand colors, logo, and favicon before or after uploading:

### A. Changing the Website Name
1. Open the `.env` file in any text editor (Notepad, VS Code, TextEdit).
2. Change the `APP_NAME` line:
   ```env
   APP_NAME="YourMarketplaceName"
   ```
3. To change the text title in the navbar and footer:
   - Open [`resources/views/layouts/app.blade.php`](resources/views/layouts/app.blade.php)
   - Search for `WorkForge` and replace it with your brand name (e.g., `GigSphere` or `HireHub`).

### B. Changing the Website Logo
- **Main SVG Logo**: Replace [`public/favicon.svg`](public/favicon.svg) with your SVG logo.
- **Main PNG / Image Logo**: Place your logo image in `public/images/logo.png`.
- In [`resources/views/layouts/app.blade.php`](resources/views/layouts/app.blade.php) (Line ~85), you can replace the `W` letter badge with an `<img>` tag:
  ```html
  <img src="{{ asset('images/logo.png') }}" alt="Your Logo" class="h-8 w-auto">
  ```

### C. Changing the Favicon (Browser Tab Icon)
1. Prepare your icon in `.ico`, `.svg`, or `.png` format.
2. Replace the following files in the `public/` directory:
   - `public/favicon.ico`
   - `public/favicon.svg`
   - `public/apple-touch-icon.png`

---

## 3. How to Configure Your Environment (`.env` File)

In the extracted folder, look for the file named `.env` (or copy `.env.example` and rename it to `.env`).

Open `.env` with a text editor and fill in your settings:

```env
# ===================================================
# 1. APPLICATION SETTINGS
# ===================================================
APP_NAME="WorkForge"
APP_ENV=production
APP_KEY=base64:7vF8t1r8f9QWl6dD6Hjkl2mNpQrStUvWxYz12345678=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# ===================================================
# 2. DATABASE SETTINGS (From your Hosting cPanel)
# ===================================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yourhosting_dbname
DB_USERNAME=yourhosting_dbuser
DB_PASSWORD=yourhosting_dbpassword

# ===================================================
# 3. SESSION & STORAGE
# ===================================================
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

# ===================================================
# 4. PAYMENT GATEWAY (Dodo Payments)
# ===================================================
DODO_PAYMENTS_API_KEY=your_dodo_live_or_test_api_key
DODO_PAYMENTS_WEBHOOK_SECRET=your_dodo_webhook_secret
DODO_PAYMENTS_ENVIRONMENT=live # Use 'test_mode' for sandbox

# ===================================================
# 5. EMAIL SETTINGS (SMTP)
# ===================================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org # or mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> 💡 **Tip**: If you don't have an `APP_KEY`, you can generate one via terminal with `php artisan key:generate` or use the pre-filled key in the package.

---

## 4. How to Create a Database in Your Hosting

If using **cPanel**:
1. Log into your **cPanel** dashboard.
2. Under the **Databases** section, click **MySQL Databases** (or **MySQL Database Wizard**).
3. **Create Database**: Enter a name (e.g. `workforge`) &rarr; Click **Create Database**.
4. **Create User**: Enter a username (e.g. `dbuser`) and a secure password &rarr; Click **Create User**.
5. **Add User to Database**: Select the user and database you just created &rarr; Check **ALL PRIVILEGES** &rarr; Click **Make Changes**.
6. Copy the **Database Name**, **Database User**, and **Password** into your `.env` file (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

---

## 5. Uploading the Files to Your Hosting

### Method A: Upload via cPanel File Manager (Recommended)
1. Zip the files on your computer if they aren't already zipped.
2. In cPanel, click **File Manager**.
3. Navigate to `public_html` (or your subdomain directory).
4. Click **Upload** and upload the zip file.
5. Right-click the uploaded zip file &rarr; select **Extract**.
6. Make sure the files are extracted directly inside `public_html`.

> ⚠️ **Important for Root Routing on Shared Hosting**:
> This script is already configured with an optimized root `.htaccess` file so that your visitors are automatically routed to the public directory securely without showing `/public/` in the URL!

### Method B: Upload via FTP (FileZilla)
1. Open FileZilla (or Cyberduck).
2. Enter your FTP Host, Username, Password, and Port (21).
3. Open `public_html` on the remote server.
4. Drag and drop all project files from your computer into `public_html`.

---

## 6. Importing Database & Running Migrations

You have two simple ways to set up the database tables:

### Option 1: Via cPanel phpMyAdmin (Easiest for Non-Technical Users)
1. In cPanel, click **phpMyAdmin**.
2. Click on your database name on the left sidebar.
3. Click the **Import** tab at the top.
4. Click **Choose File** and select the file `database/schema.sql` (or `database/database.sqlite` converted).
5. Click **Import / Go** at the bottom.

### Option 2: Via the Built-in Web Installer (1-Click, Recommended)
- **If you have SSH Terminal**: Run:
  ```bash
  php artisan migrate:fresh --seed --force
  ```
- **If you don't have SSH Terminal**: Simply visit the built-in setup URL in your browser:
  ```
  https://yourdomain.com/installer.php?secret=workforge2026
  ```
  *(This automatically creates ALL database tables, categories, skills, sample jobs, and demo users in one click. When you see the green "Setup completed successfully!" message, your marketplace is ready.)*
- **After setup, delete the `installer.php` file** from the `public/` folder so the setup wizard can't be opened again.

---

## 7. Setting Up Public Storage for Images

To make sure profile avatars, job attachments, and portfolio images load quickly:

- **Via SSH / Terminal**:
  ```bash
  php artisan storage:link
  ```
- **Via cPanel File Manager (without SSH)**:
  1. Open `public_html/public/`
  2. If a shortcut named `storage` is not present, upload this tiny one-line PHP helper as `public_html/public/storage-link.php`, open `https://yourdomain.com/storage-link.php` in your browser, then **delete the helper file**:
     ```php
     <?php symlink('../storage/app/public', __DIR__ . '/storage'); echo 'Storage link created!';
     ```
  3. Note: the built-in `installer.php` also tries to create this link automatically during setup.

- **File Permissions**:
  Ensure the following folders have **775** or **755** write permissions:
  - `storage/`
  - `storage/app/public/`
  - `storage/app/public/avatars/`
  - `bootstrap/cache/`

---

## 8. Configuring Email & Payment Gateways

### Setting Up Dodo Payments (Direct Escrow & Global Wire)
1. Sign up at [dodopayments.com](https://dodopayments.com).
2. In your Dodo Dashboard, go to **Developer Settings &rarr; API Keys**.
3. Copy your API Key and paste it into `.env`:
   ```env
   DODO_PAYMENTS_API_KEY=your_dodo_api_key_here
   ```
4. Set your Webhook URL in Dodo Payments Dashboard to:
   ```
   https://yourdomain.com/payments/dodo/webhook
   ```

### Setting Up SMTP Email (for notifications & password reset)
Fill in your email hosting details in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=support@yourdomain.com
MAIL_PASSWORD=YourEmailPassword
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="support@yourdomain.com"
MAIL_FROM_NAME="WorkForge Marketplace"
```

---

## 9. Default Admin & Demo Logins

After installation, the system comes with ready-to-test demo accounts:

| Role | Email | Password | Quick 1-Click Login |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@upwork.test` | `password` | `https://yourdomain.com/quick-login/admin` |
| **Client** | `client@upwork.test` | `password` | `https://yourdomain.com/quick-login/client` |
| **Freelancer** | `alex.dev@upwork.test` | `password` | `https://yourdomain.com/quick-login/freelancer` |

### Accessing the Super Admin Panel
Visit:
`https://yourdomain.com/admin/dashboard`

From the Super Admin panel, you can:
- Moderate and approve/suspend users and freelancers.
- Moderate job listings and categories.
- Review and release escrow contracts and milestones.
- Review and approve withdrawal payout requests.
- Resolve client-freelancer disputes.

---

## 10. Troubleshooting & Common Questions

#### Q1: I see a "500 Internal Server Error" after uploading.
- **Fix**: Check that your `.env` file exists and has correct database credentials. Make sure the folder `storage/` and `bootstrap/cache/` have **775** write permissions.

#### Q2: Uploaded avatar images or photos show broken images.
- **Fix**: Make sure you ran `php artisan storage:link` or connected `public/storage` to `storage/app/public`.

#### Q3: How do I disable the "Demo Environment Quick Switcher" bar at the top?
- **Fix**: In [`resources/views/layouts/app.blade.php`](resources/views/layouts/app.blade.php), look for lines 61–75 (`<!-- Quick Role Switcher Banner -->`) and delete or comment out that block when you are ready to go live!

#### Q4: How do I clear cache after editing files?
- In terminal:
  ```bash
  php artisan view:clear
  php artisan config:clear
  php artisan cache:clear
  ```

---

## 11. Support & Contact

Need help with installation, customization, or anything else? We're here for you!

- **Chat on WhatsApp**: <a href="https://wa.me/923459347900"><b>+92 345 9347900</b></a> — tap the link to start a WhatsApp chat (or add this number to your contacts and message us).
- **Email**: <a href="mailto:um.waqas.khan@gmail.com"><b>um.waqas.khan@gmail.com</b></a>

Please include a short description of your issue and, if possible, a screenshot so we can help you faster.

---

### 🎉 Congratulations!
Your marketplace is now live, fully branded, and ready to accept clients, freelancers, and payments!

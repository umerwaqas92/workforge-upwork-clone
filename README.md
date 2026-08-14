# ⚡ WorkForge — Upwork-Style Freelance Marketplace Platform

[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire 3](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Dodo Payments](https://img.shields.io/badge/Dodo_Payments-Merchant_of_Record-FFB800?style=for-the-badge)](https://dodopayments.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

**WorkForge** is a full-featured, enterprise-grade freelance marketplace and escrow platform built with **Laravel 11**, **Blade (SSR for SEO optimization)**, **Livewire 3**, **Alpine.js**, **Tailwind CSS**, and **Dodo Payments**.

---

## 🌟 Key Features & Systems

### 1. 🔍 Discovery & Marketplace Directory (SSR + SEO Optimized)
- **High-Performance Job Search**: Livewire instant faceted filtering by Category, Job Type (Fixed-Price vs. Hourly), Budget Range, and Experience Level.
- **Talent Directory**: Browse verified freelancers by hourly rate, Top Rated badge status, skills, and Job Success Score (JSS).
- **SEO-Friendly Architecture**: Server-Side Rendered (SSR) public job and freelancer profile pages for Google indexing.

### 2. 👤 Upwork-Standard Freelancer Profile Builder
- **Profile Completeness Engine**: Real-time progress bar (0% to 100%) and dynamic checklist of missing steps.
- **Specialized Sections**:
  - Title, Hourly Rate (`$/hr`), English Level, and Experience Level.
  - Multi-tag interactive Skills selector.
  - **Portfolio Projects Builder**: Add/remove projects with title, category, cover image, live links, and case study notes.
  - **Employment History Timeline**: Past companies, job titles, duration, and responsibilities.
  - **Education & Certifications**: Degrees, schools, and issuing authorities (e.g. AWS).
  - **Social Links**: Direct GitHub, LinkedIn, and portfolio website linkages.

### 3. 📄 Proposals & Bidding Engine
- **Platform Fee Calculator**: Automatically calculates gross bid, 10% platform fee, and net received amount.
- **Dynamic Milestone Breakdown**: Freelancers can propose custom milestones with individual amounts and durations.
- **Client Review Room**: Shortlist, decline, direct message, or hire candidates with one click.

### 4. 🛡️ Contract Workroom & Escrow Lifecycle
- **Escrow Milestone Funding**: Clients lock funds into platform escrow protection.
- **Deliverables Submission**: Freelancers submit work notes and GitHub PR links.
- **One-Click Payment Release**: Escrow balance is released to freelancer wallet minus the 10% platform fee.
- **Two-Way Reciprocal Reviews**: 5-star ratings and written feedback calculation for both clients and freelancers.

### 5. 💳 Live Payments & Multi-Currency Escrow (Dodo Payments)
- **Merchant of Record (MoR)**: Integrated with **Dodo Payments (`dodopayments/client`)** handling global taxes, EU VAT, and compliance automatically.
- **Payment Methods**: Credit/Debit Cards, Apple Pay, Google Pay, and UPI / SEPA bank transfers.
- **Double-Entry Financial Ledger**: Immutable transaction history tracking deposits, escrow locks, releases, and withdrawals.

### 6. 💬 Real-Time Messaging Hub
- Live conversation threads between clients and freelancers directly linked to jobs and active contracts with auto-polling message delivery.

### 7. ✉️ Transactional Email Engine (SMTP / Gmail)
- Automated branded HTML email dispatches for:
  - User Welcome & Onboarding
  - New Proposal notifications to clients
  - *"You're Hired!"* alerts to freelancers
  - *"Milestone Funded in Escrow"* notifications
  - Deliverables submission reviews
  - Payment release receipts

### 8. 👑 Super-Admin Control Panel
- Platform Gross Merchandise Volume (GMV) tracking.
- 10% platform take-rate revenue reporting.
- User management, job moderation, contract auditing, and payout request approvals.

---

## 👥 Demo Personas & Test Credentials

All accounts share the default password: `password`

| Persona | Email | Role | Features to Explore |
| :--- | :--- | :--- | :--- |
| **Marcus Vance** | `client@upwork.test` | `client` | Post jobs, review proposals, fund milestones, release payments |
| **Alexander Reed** | `alex.dev@upwork.test` | `freelancer` | Submit proposals, submit deliverables, edit profile, withdraw funds |
| **Sophia Chen** | `sophia.ui@upwork.test` | `freelancer` | Top-rated UI/UX designer portfolio |
| **Admin Controller** | `admin@upwork.test` | `admin` | Overview GMV, moderate jobs, audit contracts, process payouts |

---

## 🚀 Quickstart & Local Installation

### Prerequisites
- **PHP 8.2+**
- **Composer 2.x**
- **Node.js 18+ & npm**
- **SQLite / MySQL / PostgreSQL**

### 1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/workforge-upwork-clone.git
cd workforge-upwork-clone
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup & Seeding
```bash
# Creates database and seeds categories, skills, clients, freelancers, and contracts
php artisan migrate:fresh --seed
```

### 5. Build Assets & Start Development Server
```bash
# Terminal 1: Build & watch assets
npm run dev

# Terminal 2: Start Laravel server
php artisan serve
```

Visit **`http://127.0.0.1:8000`** in your browser.

---

## ⚙️ Environment Configuration (`.env`)

```env
APP_NAME="WorkForge"
APP_URL=http://127.0.0.1:8000

# Database (Default: SQLite, supports MySQL/PostgreSQL)
DB_CONNECTION=sqlite

# Dodo Payments (Merchant of Record)
DODO_PAYMENTS_API_KEY=your_dodo_api_key_here
DODO_PAYMENTS_ENVIRONMENT=test_mode # or live_mode
DODO_PAYMENTS_WEBHOOK_KEY=your_webhook_key

# Email / SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="WorkForge Marketplace"
```

---

## 🧪 Automated Testing

WorkForge includes a comprehensive feature test suite covering user authentication, job posting, Livewire searching, proposal bidding, milestone escrow funding, and payment releases:

```bash
php artisan test
```

**Result:** `12 tests passed, 23 assertions, 0 failures.`

---

## 📄 License
This project is open-source software licensed under the **MIT License**.

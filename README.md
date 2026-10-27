# Daisy — Handcrafted Jewellery E-Commerce & Admin Panel

A full-stack jewellery e-commerce platform built with **Laravel 11, PHP 8.2+, MySQL 8 (with SQLite fallback), Blade, and plain CSS/JS (Vite)**. The user interface directly reproduces the layout, colour tokens, fonts, SVG sprite artwork, and responsive behaviour of the `daisy.html` design mockup.

---

## 💎 Demo Login Credentials

| Role | Email | Password | Access URL |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@daisy.test` | `Admin@12345` | `http://127.0.0.1:8000/admin` |
| **Customer** | `customer@daisy.test` | `Customer@12345` | `http://127.0.0.1:8000/login` |

> *Note: Please change these default passwords before deploying to a production server.*

---

## 🚀 How to Run the Website Locally

### 1. Requirements
- **PHP 8.2+** with `pdo_sqlite` or `pdo_mysql`, `curl`, `fileinfo`, `mbstring`, `openssl`, `bcmath`
- **Composer** (v2.2+)
- **Node.js** (v18+) & **npm**
- **MySQL 8** (XAMPP / Laragon / MySQL Service) or **SQLite** (bundled out-of-the-box)

### 2. Quick Setup Commands
```bash
# 1. Install PHP dependencies
composer install --no-security-blocking

# 2. Configure environment file
cp .env.example .env

# 3. Generate application encryption key
php artisan key:generate

# 4. Run migrations with ready seed data (categories, 12 mockup rings, CMS, demo orders)
php artisan migrate --seed

# 5. Build frontend assets (compiled via Vite)
npm install
npm run build

# 6. Start the local development server
php artisan serve
```

The web application is now live at:
👉 **`http://127.0.0.1:8000`**

---

## 🔐 How to Open the Admin Panel

1. **Direct URL:** Go to [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin) in your web browser.
2. If you are not signed in, the `EnsureUserIsAdmin` middleware will automatically prompt you to log in.
3. Sign in using the Admin credentials:
   - **Email:** `admin@daisy.test`
   - **Password:** `Admin@12345`
4. You will be redirected immediately into the **Daisy Admin Dashboard** (`/admin`).
5. While logged in as an Admin, a pink **"Admin"** shortcut also appears in the header navigation on the customer storefront.

### Admin Panel Capabilities:
- **Dashboard:** Real-time metrics (Total sales in ₹, Orders today, Pending orders, Customers), interactive 30-day Chart.js revenue line chart, recent orders table, and low-stock alerts.
- **Products:** Full CRUD with multi-image drag-and-drop uploader, cover photo selector, size-stock matrix (Indian ring sizes 10–24), SVG fallback symbol picker (`ring`, `ring-round`, `ring-band`, `ring-twin`, `ring-marquise`, `earrings`, `necklace`, etc.), card color theme picker (`c-pink`, `c-emerald`, `c-sapphire`, `c-gold`, `c-lilac`), soft delete, and trash restore.
- **Categories & Metals:** Dynamic management of product categories and precious metals.
- **Orders:** Filter by order status, payment status, and date range; update order & payment status; add internal timestamped staff notes; export orders to CSV; and generate print-friendly tax invoices.
- **Customers:** View registered customer order counts, order history, and block/unblock accounts.
- **Reviews:** Moderate customer reviews with 1-click Approve / Hide / Delete actions.
- **Coupons:** Configure percentage or flat discounts with minimum order thresholds and expiry limits.
- **Homepage CMS:** Live editing of hero slides, scheduled drops, social rail cards, and scrolling word marquee.
- **Settings:** Update brand name, taglines, contact details, store hours, social links, free delivery threshold, and delivery fees.
- **Inbox:** Read contact messages and view/export newsletter subscriber lists.

---

## 🗄️ Database Setup (MySQL & SQLite)

The repository comes pre-configured to work with **SQLite** for zero-configuration local evaluation, while fully supporting **MySQL 8** for production.

### Using MySQL 8 (XAMPP / Laragon / Standalone):
1. Open XAMPP / Laragon and start the **MySQL** service.
2. Create a database called `daisy_db` in phpMyAdmin or MySQL CLI:
   ```sql
   CREATE DATABASE daisy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. In your `.env` file, ensure the database settings match your local MySQL configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=daisy_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Run migrations and seed data:
   ```bash
   php artisan migrate:fresh --seed
   ```

---

## 📸 Adding Real Product Photos

1. Navigate to **Admin Panel → Products → Add New Piece** (`/admin/products/create`) or edit an existing piece.
2. In the **Photos** section, drag and drop up to 10 image files (`.jpg`, `.png`, `.webp`).
3. Images are previewed instantly. Click the **Cover** button on any tile to set it as the primary cover photo for the listing grid.
4. Resized variants (`medium` 900px and `thumb` 400px) are automatically generated using `intervention/image`.
5. If a product does not have uploaded photos, it gracefully displays the SVG `<symbol>` artwork corresponding to its selected `art_symbol` and `color_class`.

---

## 💳 Razorpay Test-Mode Integration

Online payments (UPI and Card) are wired behind a clean `PaymentGatewayInterface` with a test-mode simulator in `app/Services/Payment/RazorpayPaymentGateway.php`.

To connect live or test Razorpay API keys:
1. Obtain your API Key and Secret from the [Razorpay Dashboard](https://dashboard.razorpay.com).
2. Add them to your `.env` file:
   ```env
   RAZORPAY_KEY=rzp_test_your_key_here
   RAZORPAY_SECRET=your_secret_key_here
   RAZORPAY_TEST_MODE=true
   ```

---

## 🧪 Running Automated Tests

The application includes automated Feature tests covering product listing filters, dynamic search, cart additions, coupon discounts, DB transaction checkout with stock locking, and admin access control:

```bash
php artisan test
```

All 32 test cases (92 assertions) pass.

---

## 📁 Key Directory Structure

```text
kira/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin dashboard, products, orders, CMS, inbox
│   │   │   ├── Auth/           # Restyled Breeze authentication controllers
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── HomeController.php
│   │   │   ├── ProductController.php
│   │   │   └── WishlistController.php
│   │   └── Middleware/
│   │       └── EnsureUserIsAdmin.php
│   ├── Models/                 # Eloquent models with relationships & scopes
│   └── Services/
│       ├── CartService.php     # Cart calculations, guest-to-user cart merge
│       ├── CheckoutService.php # Atomic DB transactions & row-lock stock decrement
│       ├── ImageService.php    # Multi-resolution image processing
│       └── Payment/            # Razorpay gateway & interface
├── database/
│   ├── migrations/             # All 18 relational tables
│   └── seeders/DatabaseSeeder.php # Mockup catalogue, sample drops & orders
├── resources/
│   ├── css/
│   │   ├── app.css             # Exact mockup CSS tokens, light/dark mode
│   │   └── admin.css           # Clean, brand-consistent admin stylesheet
│   ├── js/
│   │   ├── app.js              # Rails sliding, drawer AJAX, toast notifications
│   │   ├── shop.js             # Filters, sort, live search, quick-view modal
│   │   └── admin.js            # Image dropzone, Chart.js analytics, HTML modals
│   └── views/
│       ├── admin/              # Dashboard, orders, products, CMS, settings
│       ├── layouts/            # app.blade.php, admin.blade.php, guest.blade.php
│       ├── pages/              # home, shop, product-detail, checkout, account
│       └── partials/           # svg-sprite.blade.php, bag-drawer, product-card
└── routes/
    ├── web.php                 # Storefront & Admin routes
    └── auth.php                # Authentication routes
```

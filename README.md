# 💎 AuraAssets - Premium Digital Marketplace

A centralized, multi-vendor marketplace for digital creators to sell assets (UI kits, 3D models, Templates) and for customers to purchase them securely. Built with **Laravel 11**, **FilamentPHP**, and **Google Gemini AI**.

![AuraAssets Banner](https://dummyimage.com/1200x400/1e1b4b/ffffff&text=AuraAssets+Marketplace)

---

## 🌟 Key Features

### 🛒 Customer Experience
*   **Modern Storefront**: High-performance, SEO-optimized marketplace with "Dark Mode" aesthetics.
*   **🧠 Smart Search**: Powered by **Google Gemini**. Search for "spooky" and find "Halloween" items via semantic expansion.
*   **💳 Wallet System**: Top-up funds securely via Razorpay and enjoy 1-click checkout.
*   **Instant Downloads**: Validated secure download links with expiration logic.
*   **User Dashboard**: "My Library" to manage purchases, invoices, and wishlist.

### 🎨 Creator Panel (Vendor Portal)
*   **Dedicated Creator Dashboard**: Built with FilamentPHP.
*   **✨ Creator Copilot**: AI-powered tool that analyzes uploading images and **auto-generates** SEO descriptions and tags.
*   **Real-time Analytics**: Track views, sales, and revenue.
*   **Payouts**: Request money withdrawal to your bank account; admins manage approval.

### 🛡️ Admin & Security
*   **Comprehensive Admin Panel**: Manage Users, Shops, Products, Categories, and Finances.
*   **Security**: Product files are **Encrypted** on storage.
*   **Role-Based Access**: Strict separation between Admin, Creator, and Customer roles.

---

## 🛠️ Tech Stack

*   **Framework**: [Laravel 11](https://laravel.com)
*   **Admin/Creator Panels**: [FilamentPHP v3](https://filamentphp.com)
*   **Frontend**: Blade + [Livewire](https://livewire.laravel.com) + [Alpine.js](https://alpinejs.dev)
*   **Styling**: [TailwindCSS](https://tailwindcss.com)
*   **AI Integration**: Google Gemini API (via `gemini-php`)
*   **Payments**: Razorpay (Gateway) + Internal Wallet System

---

## 🚀 Installation Guide

### Prerequisites
*   PHP 8.2+
*   Composer
*   Node.js & NPM
*   MySQL 8.0+

### Steps

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/yourusername/aura-assets.git
    cd aura-assets
    ```

2.  **Install Backend Dependencies**
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies**
    ```bash
    npm install
    npm run build
    ```

4.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    php artisan storage:link
    ```

5.  **Database Setup**
    *   Create a clean MySQL database.
    *   Update `.env` `DB_` credentials.
    *   Run migrations and seeders:
    ```bash
    php artisan migrate --seed
    ```
    *   *(Optional)* Run the setting seeder if setting up for the first time:
    ```bash
    php artisan db:seed --class=UpdateSettingsSeeder
    ```

6.  **Serve Application**
    ```bash
    php artisan serve
    ```
    Visit `http://localhost:8000`

---

## ⚙️ Configuration (.env)

Ensure these variables are set for full functionality:

### 🤖 Google Gemini (AI Features)
```env
GEMINI_API_KEY="your_google_ai_studio_key"
```

### 💳 Razorpay (Payments)
```env
RAZORPAY_KEY="rzp_test_..."
RAZORPAY_SECRET="your_secret"
```

### 📧 Mail (For Notifications)
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
# ...
```

---

## 👥 User Roles & Access

| Role | Login URL | Default Credentials (if seeded) |
| :--- | :--- | :--- |
| **Admin** | `/admin` | `admin@vivizmart.com` / `password` |
| **Creator** | `/creator` | `creator@vivizmart.com` / `password` |
| **Customer** | `/` (Login Modal) | `customer@vivizmart.com` / `password` |

---

## 🧪 AI Features Usage

### Smart Search
1.  Go to the Homepage.
2.  Search for a concept (e.g., "scary").
3.  The system will expand this to ["horror", "dark", "ghost"] and find related items.

### Creator Copilot
1.  Log in as **Creator**.
2.  Go to **Products -> New Product**.
3.  Enter a **Name** and upload a **Preview Image**.
4.  Click **"✨ Magic Generate"** inside the Description field.
5.  Watch as AI fills your description and tags!

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

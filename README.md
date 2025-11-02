🏫 URL Shortener System — Laravel + AngularJS

This project is a multi-company URL Shortener system built using Laravel 12 + and AngularJS 1.8 for the frontend.
It includes authentication, authorization, company & user management, invitation handling, and secure short URL redirection.

🚀 Features
🧑‍💼 User & Role Management

Roles: SuperAdmin, Admin, Manager, Sales, Member

SuperAdmin → can create companies & invite Admins

Admin → can invite Sales and Managers within their company

🏢 Company Management

Create and list multiple companies (unique name per company)

Assign users to their respective companies

🔗 URL Shortener

Admin / Managers / Sales can create short URLs

Public redirect via /s/{short_code} route

Clicks tracking and active/inactive status

SuperAdmin all URL viewing permissions

Admin own company URL viewing permissions

sales or manager thenselves URL viewing permissions

✉️ Invitation System

Invitation sent by Admins or SuperAdmins

Auto user creation upon invitation

Tracks status: Pending, Accepted, Rejected

🔐 Authentication

Custom AngularJS login page

Laravel session-based authentication

Redirects unauthorized users to /login

⚙️ Tech Stack
Component	Technology
Backend	Laravel 11 / 12
Frontend	AngularJS 1.8 + Blade
Database	MySQL / SQLite
Authentication	Laravel Auth (Session)
Frontend Design	HTML / CSS / AngularJS
🧩 Project Setup Guide
1️⃣ Clone the Repository

git clone https://github.com/yourusername/shortener.git
cd shortener

2️⃣ Install PHP Dependencies

Make sure you have Composer installed, then run:

composer install

4️⃣ Create Environment File

Copy .env.example to .env

cp .env.example .env


Then update your database credentials:

APP_NAME="URL Shortener"
APP_URL=http://localhost/shortener
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shortener
DB_USERNAME=root
DB_PASSWORD=

5️⃣ Generate Application Key
php artisan key:generate

6️⃣ Run Database Migrations and Seeders
php artisan migrate --seed


✅ This will:

Create all tables

Seed a SuperAdmin user (superadmin@example.com / password123)

7️⃣ Start Local Development Server
php artisan serve


Project will run at:
👉 http://localhost:8000

(or http://localhost/shortener
 if using XAMPP)

🔑 Default Login Credentials
Role : SuperAdmin	
Email : superadmin@example.com
Password : password123

🧠 Project Structure Overview
shortener/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── CompanyController.php
│   │   │   ├── InvitationController.php
│   │   │   └── ShortUrlController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Company.php
│   │   ├── Invitation.php
│   │   └── ShortUrl.php
│
├── resources/
│   ├── views/
│   │   ├── login.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── companies.blade.php
│   │   ├── invitations.blade.php
│   │   └── short_urls.blade.php
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── public/
│   └── js/
│       └── angular-app.js
│
├── database/
│   ├── migrations/
│   └── seeders/
│
└── README.md

🌍 Public Short URL Redirects

Once a short URL is generated,
the system creates a link like:

http://localhost/shortener/s/abc12345


Anyone visiting that link is automatically redirected to the original long URL.

🧪 Testing the Project

Register / Login as SuperAdmin

Create a company and invite an Admin

Login as Admin → Invite a Manager / Sales

Login as Manager → Create short URLs

Visit the generated /s/{short_code} links publicly — verify redirects

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

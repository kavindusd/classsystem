# ClassSystem — Premium PHP MVC Portal

A glassmorphic "Bento-Box" management portal for Students, Teachers, and Admins.

## 🚀 Quick Setup

1. **Database**: Create a database named `classystem_db`.
2. **Environment**: Copy `.env.example` to `.env` and fill in your DB credentials.
3. **Migrate**: Run `php database/migrate.php` from your terminal to build the tables.
4. **Server**: Run `php -S localhost:8000` in the root directory.
5. **Login**: 
   - **Admin**: `admin@classystem.com` / `admin123`

## 🛠️ Key Configurations

* **Admin Dashboard**: Manage Brand Name, SMTP, and WhatsApp API keys directly in the UI.
* **WhatsApp**: Supports Twilio, UltraMsg, or Simulation Mode for testing.
* **Phone Login**: Can be toggled on/off globally by the administrator.

## 📁 File Structure

```text
├── app/
│   ├── core/           # Framework base classes (Router, DB, Model)
│   ├── controllers/    # Route handlers (Admin, Teacher, Student)
│   ├── models/         # Database interaction logic
│   ├── middleware/     # Role-based access control
│   └── helpers/        # Utilities (Mail, WhatsApp, OTP)
├── config/             # App & Database settings
├── database/           # SQL schema and Migration scripts
├── public/             # CSS/JS assets and User uploads
├── routes/             # web.php (All system routes)
└── storage/            # System logs and cache
```

## ⚖️ License
MIT. Free to use for academic or commercial purposes.

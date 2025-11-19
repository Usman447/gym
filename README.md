# Gym Management System

A comprehensive gym management system built with Laravel 5.1, featuring member management, subscription tracking, payment processing, attendance monitoring, and automated WhatsApp reminders.

## Features

### Core Modules

- **Member Management**
  - Add, edit, and manage member profiles
  - Track member status and information
  - Member photo uploads and media management

- **Subscription Management**
  - Create and manage subscription plans
  - Track subscription start/end dates
  - Renew, cancel, upgrade, or downgrade subscriptions
  - Multiple subscription support per member

- **Payment & Invoice Management**
  - Generate invoices automatically
  - Track payment details and history
  - Support for multiple payment methods
  - Cheque payment tracking
  - Outstanding payment monitoring
  - Print invoices

- **Plan Management**
  - Create and customize membership plans
  - Set pricing and duration
  - Plan-based subscription assignment

- **Attendance Tracking**
  - Monitor member attendance
  - Integration with ZKTeco attendance devices
  - Attendance history and reports

- **Enquiry Management**
  - Track potential member enquiries
  - Follow-up management
  - Enquiry status tracking (Lead, Lost, Member)
  - Transfer enquiries between staff

- **Expense Management**
  - Track gym expenses
  - Categorize expenses
  - Expense reporting and analytics

- **Food & Inventory Management**
  - Manage food orders
  - Track inventory items
  - Food item catalog

- **Services Management**
  - Manage additional gym services
  - Service-based subscriptions

- **SMS & WhatsApp Automation**
  - Automated WhatsApp reminders for subscription renewals
  - SMS event triggers
  - Customizable message templates
  - Message history tracking

- **Dashboard & Analytics**
  - Quick stats overview
  - Revenue and collection tracking
  - Member registration trends
  - Outstanding payments monitoring
  - Interactive charts and graphs

- **User & Role Management**
  - Role-based access control (RBAC)
  - Granular permission system
  - User management with assigned roles
  - Secure authentication with JWT

## Technology Stack

### Backend
- **PHP**: >= 5.5.9
- **Laravel Framework**: 5.1.*
- **Database**: MySQL
- **Authentication**: JWT (tymon/jwt-auth)
- **RBAC**: Entrust (Zizaco/entrust)
- **Image Processing**: Intervention Image
- **Media Library**: Spatie Laravel Media Library

### Frontend
- **Bootstrap**: 3.x
- **Build Tool**: Gulp with Laravel Elixir
- **JavaScript**: jQuery (included via Laravel)

### Automation Scripts
- **Python**: 3.x
- **Twilio SDK**: For WhatsApp messaging
- **MySQL Connector**: For database connectivity

### Development Tools
- **Testing**: PHPUnit
- **Debugging**: Laravel Debugbar
- **Error Tracking**: Sentry

## Requirements

### Server Requirements
- PHP >= 5.5.9
- MySQL >= 5.6
- Composer
- Node.js & NPM (for frontend assets)
- Python 3.x (for automation scripts)

### PHP Extensions
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- GD Library or Imagick

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd gym
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Configuration

Create a `.env` file in the root directory:

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database and other settings in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gym_db
DB_USERNAME=gym_user
DB_PASSWORD=your_password
```

### 4. Database Setup

Run migrations:

```bash
php artisan migrate
php artisan db:seed
```

### 5. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

### 8. Setup Python Automation Scripts

#### Install Python Dependencies

```bash
cd python_scripts
python3 -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
pip install -r requirements.txt
```

#### Configure Database Connection

Edit `whatsapp_automation_twilio.py` and update the database configuration:

```python
DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'database': 'gym_db',
    'user': 'gym_user',
    'password': 'your_password'
}
```

#### Setup Cron Jobs

Run the setup script:

```bash
chmod +x setup_cron_jobs.sh
./setup_cron_jobs.sh
```

Or manually add to crontab:

```bash
crontab -e
```

Add the following line (runs every 30 minutes):

```
*/30 * * * * cd /path/to/gym/python_scripts && /path/to/venv/bin/python whatsapp_automation_twilio.py >> /path/to/gym/python_scripts/logs/whatsapp_cron.log 2>&1
```

## Configuration

### WhatsApp Automation Settings

Configure WhatsApp settings through the admin panel or directly in the database `trn_settings` table:

- `whatsapp_automation_enabled`: Enable/disable automation (0/1)
- `whatsapp_api_key`: Twilio Account SID
- `whatsapp_api_secret`: Twilio Auth Token
- `whatsapp_from_number`: Twilio WhatsApp number (format: whatsapp:+1234567890)
- `whatsapp_reminder_2_days`: Days after end date for 2nd reminder (default: 5)
- `whatsapp_reminder_3_days`: Days after 2nd reminder for 3rd reminder (default: 7)
- `whatsapp_reminder_1_message`: Message template for 1st reminder
- `whatsapp_reminder_2_message`: Message template for 2nd reminder
- `whatsapp_reminder_3_message`: Message template for 3rd reminder
- `whatsapp_start_time`: Start time for sending messages (HH:MM format)
- `whatsapp_end_time`: End time for sending messages (HH:MM format)

### Message Template Variables

Use these variables in your message templates:
- `{member_name}`: Member's name
- `{end_date}`: Subscription end date (DD-MM-YYYY format)
- `{days_ago}`: Days since subscription ended

Example:
```
Hello {member_name}, your subscription ended on {end_date}. Please renew to continue enjoying our services.
```

## Usage

### Accessing the Application

1. Start the development server:

```bash
php artisan serve
```

2. Access the application at `http://localhost:8000`

3. Login with your admin credentials (create admin user if needed)

### Creating Admin User

You can create an admin user using the provided script:

```bash
php create_user_with_permission.php
```

### Manual WhatsApp Script Execution

To test the WhatsApp automation script manually:

```bash
cd python_scripts
source venv/bin/activate
python whatsapp_automation_twilio.py
```

## Project Structure

```
gym/
├── app/                    # Application core
│   ├── Http/
│   │   └── Controllers/   # Application controllers
│   ├── Models/            # Eloquent models
│   └── Services/          # Business logic services
├── bootstrap/             # Bootstrap files
├── config/                # Configuration files
├── database/
│   ├── migrations/        # Database migrations
│   └── seeds/            # Database seeders
├── public/                # Public assets and entry point
├── resources/
│   ├── views/            # Blade templates
│   └── assets/           # Frontend assets (CSS, JS)
├── python_scripts/        # Python automation scripts
│   ├── whatsapp_automation_twilio.py
│   ├── requirements.txt
│   └── logs/
├── storage/               # Storage for logs, cache, uploads
├── tests/                 # PHPUnit tests
├── vendor/                # Composer dependencies
├── artisan               # Laravel command-line tool
├── composer.json         # PHP dependencies
├── package.json          # Node.js dependencies
└── README.md            # This file
```

## Key Features Explained

### Role-Based Access Control

The system uses Entrust for RBAC with granular permissions. Key permissions include:
- `manage-gymie`: Full system access
- `manage-members`: Member management
- `manage-subscriptions`: Subscription management
- `manage-payments`: Payment management
- `manage-invoices`: Invoice management
- And many more...

### WhatsApp Automation

The WhatsApp automation script:
- Runs every 30 minutes (configurable)
- Sends 3 reminder messages for expiring subscriptions
- Respects time windows (only sends during configured hours)
- Tracks message history to avoid duplicates
- Supports retry for failed messages
- Formats Pakistan phone numbers automatically (+92 format)

### Attendance Monitoring

The system supports integration with ZKTeco attendance devices through a Python service that:
- Monitors attendance data
- Syncs with the database
- Runs as a systemd service

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

2. **Composer Memory Limit**
   ```bash
   php -d memory_limit=-1 /usr/bin/composer install
   ```

3. **Python Script Not Running**
   - Check if virtual environment is activated
   - Verify database credentials in the script
   - Check logs in `python_scripts/logs/`
   - Ensure Twilio credentials are configured

4. **WhatsApp Messages Not Sending**
   - Verify Twilio credentials in database settings
   - Check if automation is enabled
   - Verify phone number format (Pakistan: 03XX-XXXXXXX)
   - Check time window settings
   - Review logs for error messages

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Security

If you discover any security vulnerabilities, please email the maintainers instead of using the issue tracker.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please open an issue in the GitHub repository or contact the development team.

## Acknowledgments

- Laravel Framework
- Bootstrap
- Twilio for WhatsApp API
- All contributors and open-source libraries used in this project


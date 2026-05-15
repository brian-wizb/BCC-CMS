# BCC CMS (Kanisa Box)

A modern church management system built with Laravel to support administration, membership, finance, communication, and pastoral workflows in one platform.

## Overview

BCC CMS is designed to help churches and faith-based organizations manage day-to-day operations efficiently while keeping records clean, auditable, and easy to access.

Core goals:
- Centralize church data and operations.
- Improve visibility through dashboards and reports.
- Enable secure, role-based access for staff and leaders.
- Support communication and follow-up activities at scale.

## Features

- Member and family management
- Attendance tracking
- Donations, income, expenditure, and departmental finance records
- Pledges and pledge payment tracking
- Payroll and payroll categories
- Event management and event registrations
- Communication management (including SMS/WhatsApp integrations)
- Pastoral care cases and notes
- Follow-up task management
- Prayer requests
- Volunteer and zone management
- Role and permission management
- Audit logging and operational traceability
- Dashboard analytics and reporting

## Tech Stack

- Backend: Laravel 12, PHP 8.2+
- Frontend tooling: Vite, Tailwind CSS 4, Chart.js
- Integrations: Twilio SDK, QR code generation via chillerlan/php-qrcode
- Testing: PHPUnit

## Project Structure

High-level directories:

- app/ - Core application code (controllers, models, services, jobs, mail)
- config/ - Application configuration (auth, queue, permissions, services)
- database/ - Migrations, factories, and seeders
- resources/ - Frontend assets and Blade views
- routes/ - Web and console routes
- tests/ - Feature and unit tests
- docs/ - Migration and feature planning documents

## Getting Started

### Prerequisites

- PHP 8.2 or later
- Composer
- Node.js and npm
- MySQL, MariaDB, or PostgreSQL

### Installation

1. Clone the repository:

```bash
git clone https://github.com/<your-username>/<your-repo>.git
cd <your-repo>
```

2. Run the automated setup script:

```bash
composer run setup
```

This script installs dependencies, creates .env (if missing), generates the app key, runs migrations, installs frontend packages, and builds assets.

3. Start the development environment:

```bash
composer run dev
```

This starts:
- Laravel development server
- Queue listener
- Log viewer (Pail)
- Vite dev server

## Environment Configuration

Set required values in your .env file, including:

- APP_NAME, APP_ENV, APP_URL
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- MAIL_* settings
- TWILIO_* settings (if using SMS/WhatsApp features)
- QUEUE_CONNECTION

## Database and Queues

Run migrations manually when needed:

```bash
php artisan migrate
```

Run queue workers manually when needed:

```bash
php artisan queue:listen --tries=1 --timeout=0
```

## Testing

Run the full test suite:

```bash
composer test
```

Or:

```bash
php artisan test
```

## Documentation

Additional project documentation is available in the docs directory:

- docs/feature-migration-phases.md
- docs/nextjs-to-laravel-migration-master-plan.md

## Why This Project

This project demonstrates practical software engineering in a real domain:

- Domain modeling for complex business rules
- Access control and security-oriented design
- Background job processing
- Third-party service integration
- Data reporting and operational analytics

## Contributing

Contributions are welcome. Please open an issue or submit a pull request for improvements.

## License

This project is open-source and available under the MIT License.

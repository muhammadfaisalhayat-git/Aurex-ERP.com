# Aurex ERP - Enterprise Resource Planning System

<p align="center">
  <img src="public/images/logo.png" alt="Aurex ERP Logo" width="200">
</p>

<p align="center">
  <strong>A comprehensive bilingual (English/Arabic) ERP system built with Laravel & PostgreSQL</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#quick-start">Quick Start</a> •
  <a href="#modules">Modules</a> •
  <a href="#deployment">Deployment</a> •
  <a href="#documentation">Documentation</a>
</p>

---

## Features

- **Bilingual Support**: Full English and Arabic (RTL) interface
- **Multi-Branch**: Manage multiple business locations
- **Multi-Warehouse**: Track inventory across warehouses
- **Role-Based Access Control**: Granular permissions with Spatie
- **Audit Logging**: Complete transaction history
- **Tax-Inclusive Pricing**: Automatic tax calculations
- **Document Management**: Upload and manage attachments
- **Approval Workflows**: For supplier/customer registrations
- **Comprehensive Reporting**: Sales, purchases, inventory reports
- **Demo Data**: Pre-loaded with realistic sample data

---

## Quick Start

### Option 1: Docker Deployment (Recommended - 5 minutes)

**Windows:**
```powershell
# Download and extract the project
cd aurex-erp
.\deploy.ps1
```

**macOS/Linux:**
```bash
# Download and extract the project
cd aurex-erp
chmod +x deploy.sh
./deploy.sh
```

**Access:** http://localhost:8080

### Option 2: Manual Installation

```bash
# Clone repository
git clone https://github.com/your-repo/aurex-erp.git
cd aurex-erp

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env, then:
php artisan migrate --seed

# Start server
php artisan serve
```

**Access:** http://localhost:8000

### Default Login
- **Email**: admin@aurex.com
- **Password**: admin123

---

## Modules

### Sales Module
- **Quotations**: Create and manage customer quotations
- **Sales Orders**: Convert quotations to orders
- **Invoices**: Generate invoices from orders
- **Customers**: Customer management with registration workflow
- **Customer Registration**: Self-registration with approval

### Purchases Module
- **Purchase Orders**: Manage vendor purchases
- **Supply Orders**: Track supply requests
- **Local Purchases**: Internal/local purchase tracking
- **Vendors**: Supplier management
- **Supplier Registration**: Self-registration with approval

### Inventory Module
- **Items**: Product catalog with variants
- **Warehouses**: Multi-location inventory
- **Stock Movements**: Track all inventory changes
- **Stock Adjustments**: Correct inventory discrepancies
- **Item Transfers**: Move stock between warehouses

### Transport Module
- **Vehicles**: Fleet management
- **Drivers**: Driver assignments
- **Shipments**: Track deliveries
- **Routes**: Delivery route planning

### Maintenance Module
- **Work Orders**: Maintenance task management
- **Schedules**: Preventive maintenance
- **Assets**: Equipment tracking

### Reports Module
- **Sales Reports**: By customer, item, date
- **Purchase Reports**: By supplier, local purchases
- **Inventory Reports**: Stock levels, movements
- **Financial Reports**: Ledgers, transactions

### Administration
- **Users**: User management
- **Roles & Permissions**: Access control
- **Branches**: Multi-location setup
- **Settings**: System configuration

---

## Deployment

### Local Deployment
See [Docker Deployment Guide](#docker-deployment) above.

### Production Deployment

#### Hostinger
See [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md#hostinger-deployment)

#### GoDaddy
See [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md#godaddy-deployment)

#### Other Hosting Providers
See [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) for general deployment instructions.

---

## Documentation

- [Deployment Guide](DEPLOYMENT_GUIDE.md) - Complete deployment instructions
- [User Manual](docs/USER_MANUAL.md) - End-user documentation
- [API Documentation](docs/API.md) - API reference
- [Development Guide](docs/DEVELOPMENT.md) - Developer documentation

---

## Tech Stack

| Component | Technology |
|-----------|------------|
| Backend | Laravel 11 (PHP 8.2+) |
| Database | PostgreSQL 16 |
| Cache | Redis |
| Frontend | Blade, Bootstrap 5, jQuery |
| Auth | Spatie Laravel Permission |
| Search | Laravel Scout (optional) |

---

## Demo Data

The system includes comprehensive demo data:

- **5 Branches**: Main Office, Warehouse A, Warehouse B, Retail Store, Online Store
- **8 Warehouses**: Distributed across branches
- **50+ Items**: Various product categories
- **10+ Customers**: With contact details and addresses
- **8+ Suppliers**: With registration documents
- **Sample Transactions**: Quotations, orders, invoices, purchases
- **Registrations**: Pending and approved supplier/customer registrations

---

## Screenshots

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Sales Invoice
![Invoice](docs/screenshots/invoice.png)

### Inventory Management
![Inventory](docs/screenshots/inventory.png)

### Reports
![Reports](docs/screenshots/reports.png)

---

## System Requirements

### Minimum Requirements
- PHP 8.2+
- PostgreSQL 14+
- 2GB RAM
- 10GB Storage

### Recommended
- PHP 8.3
- PostgreSQL 16
- 4GB+ RAM
- SSD Storage

---

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

This project is licensed under the MIT License - see [LICENSE](LICENSE) file.

---

## Support

For support, email support@aurex.com or join our Slack channel.

---

## Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Spatie](https://spatie.be) - Permission package
- [Bootstrap](https://getbootstrap.com) - Frontend framework
- [PostgreSQL](https://postgresql.org) - Database

---

<p align="center">
  <strong>Built with ❤️ by Aurex Team</strong>
</p>

<p align="center">
  <a href="https://aurex.com">Website</a> •
  <a href="https://docs.aurex.com">Documentation</a> •
  <a href="https://support.aurex.com">Support</a>
</p>

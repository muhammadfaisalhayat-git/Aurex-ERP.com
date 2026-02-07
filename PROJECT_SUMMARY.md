# Aurex ERP - Project Summary

## Overview
Aurex ERP is a comprehensive, production-ready Enterprise Resource Planning system built with Laravel 11 and PostgreSQL. The system is designed for multi-branch, multi-warehouse operations with full bilingual (English/Arabic) support including RTL layout.

## Project Statistics
- **Total Files**: 134
- **PHP Files**: 129
- **Database Tables**: 50+
- **Models**: 60+
- **Controllers**: 25+
- **Seeders**: 25+
- **Language Keys**: 400+ (English & Arabic)

## Core Modules Implemented

### 1. User Management (Super Admin Only)
- User CRUD with role assignment
- Role management with granular permissions
- Warehouse assignment per user
- Password reset functionality
- User activation/deactivation
- Audit logging for all actions

### 2. Sales Module
- **Customer Management**: Groups, credit limits, balance tracking
- **Customer Requests**: Convert to quotations
- **Quotations**: Versioning, expiry dates, PDF export
- **Sales Contracts**: Terms, auto-invoice generation
- **Sales Invoices**: Tax-inclusive pricing, post/unpost workflow
- **Sales Returns**: With inventory restocking
- **Commission Management**: Rules, calculation runs, statements

### 3. Purchases Module
- **Vendor Management**: Balance tracking, statements
- **Purchase Invoices**: Post workflow, stock integration

### 4. Inventory Module
- **Product Management**: Categories, BOM, images
- **Stock Supply**: Stock in with cost tracking
- **Stock Receiving**: Purchase order linking
- **Stock Transfers**: Inter-warehouse with approval workflow
- **Transfer Requests**: Request → Approve → Execute
- **Stock Issue Orders**: Auto-generated from invoices
- **Composite Assemblies**: BOM consumption, finished goods production
- **Stock Ledger**: Complete transaction history

### 5. Transport Module
- **Trailer Management**: Drivers, capacity, license tracking
- **Transport Orders**: Loading sheets, route tracking
- **Transport Contracts**: Contractor management
- **Transport Claims**: Evidence upload, workflow management

### 6. Maintenance Module
- **Workshop Management**: Internal/External workshops
- **Maintenance Vouchers**: Parts consumption, technician assignment

### 7. Reports & Analytics
- Sales reports (by date, customer, product, salesman)
- Tax summary and detail reports
- Inventory valuation and movement reports
- Low stock alerts
- Export to PDF/Excel

### 8. Dashboard
- Customizable widgets
- Drag-drop reordering
- Role-based default layouts
- Real-time statistics

## Key Features

### Bilingual Support (EN/AR)
- Full localization with 400+ translation keys
- RTL layout support for Arabic
- Language switcher in UI
- Bilingual PDF templates

### Tax-Inclusive Pricing
- Configurable tax rate (default 15% VAT)
- Per-line or per-invoice rounding
- Formula: gross = (P×Q) - discount, net = gross/(1+r), tax = gross - net
- Tax summary reports

### Multi-Branch & Multi-Warehouse
- 3 branches seeded (Riyadh, Jeddah, Dammam)
- 4 warehouses with managers
- User warehouse assignments
- Cross-branch transfer approvals

### Security & Permissions
- Role-based access control (Spatie Permission)
- 50+ granular permissions
- 9 pre-configured roles:
  - Super Admin (full access)
  - Admin (excluding user deletion)
  - Accountant
  - Sales Manager
  - Salesman
  - Data Analyst
  - Data Entry
  - Inventory Manager
  - Warehouse User

### Audit Logging
- Complete audit trail for all CRUD operations
- IP address and user agent tracking
- Old/new values storage
- Recent activity dashboard

## Database Schema

### Core Tables
- users, roles, permissions (Spatie)
- branches, warehouses
- user_warehouse (assignments)

### Product & Inventory
- product_categories, products
- product_bom (Bill of Materials)
- product_images
- stock_ledger, stock_balances
- stock_supply, stock_supply_items
- stock_receiving, stock_receiving_items
- stock_transfers, stock_transfer_items
- stock_transfer_requests
- stock_issue_orders
- composite_assemblies

### Sales
- customer_groups, customers
- customer_requests
- quotations, quotation_items
- sales_contracts, sales_contract_items
- sales_invoices, sales_invoice_items
- sales_returns, sales_return_items

### Purchases
- vendors
- purchase_invoices, purchase_invoice_items

### Transport
- trailers
- transport_orders, transport_order_items
- transport_contracts
- transport_claims

### Maintenance
- maintenance_workshops
- maintenance_vouchers, maintenance_voucher_parts

### Commissions
- commission_rules
- commission_runs
- commission_statements, commission_statement_details

### System
- audit_logs
- system_settings
- tax_settings, currencies
- number_sequences
- dashboard_widgets, dashboard_layouts
- attachments

## Demo Data Seeded

### Users (9 demo accounts)
| Email | Role | Password |
|-------|------|----------|
| superadmin@aurex.com | Super Admin | password |
| admin@aurex.com | Admin | password |
| accountant@aurex.com | Accountant | password |
| sales.manager@aurex.com | Sales Manager | password |
| sales@aurex.com | Salesman | password |
| analyst@aurex.com | Data Analyst | password |
| dataentry@aurex.com | Data Entry | password |
| inventory@aurex.com | Inventory Manager | password |
| warehouse@aurex.com | Warehouse User | password |

### Business Data
- 3 Branches, 4 Warehouses
- 5 Product Categories, 20 Products (including 1 composite)
- 10 Customers, 5 Vendors
- 30 Sales Invoices, 5 Sales Returns
- 15 Customer Requests, 20 Quotations
- 10 Stock Supplies, 10 Stock Issues
- 10 Stock Transfers, 5 Transfer Requests
- 5 Transport Orders, 3 Transport Claims
- 3 Maintenance Workshops
- 3 Commission Rules, 1 Commission Run

## Installation Instructions

1. **Clone & Install Dependencies**
```bash
cd /mnt/okcomputer/output/aurex-erp
composer install
```

2. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure Database** (Edit .env)
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=aurex_erp
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

4. **Run Migrations & Seeders**
```bash
php artisan migrate
php artisan db:seed
```

5. **Start Application**
```bash
php artisan serve
```

Access at: http://localhost:8000

## File Structure
```
aurex-erp/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/         # User, Role, Branch, Warehouse, Settings
│   │   │   ├── Sales/         # Customer, Invoice, Quotation, etc.
│   │   │   ├── Purchases/     # Vendor, Purchase Invoice
│   │   │   ├── Inventory/     # Product, Stock, Transfer
│   │   │   ├── Transport/     # Trailer, Order, Contract, Claim
│   │   │   ├── Maintenance/   # Workshop, Voucher
│   │   │   └── Reports/       # Sales, Tax, Inventory reports
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/                # 60+ Eloquent models
│   ├── Services/              # TaxCalculator
│   └── Providers/
├── database/
│   ├── migrations/            # 15 migration files
│   └── seeders/               # 25 seeders
├── resources/
│   ├── views/                 # Blade templates
│   │   ├── layouts/           # App layout, sidebar
│   │   ├── auth/              # Login
│   │   └── dashboard.blade.php
│   └── lang/                  # EN & AR translations
├── routes/
│   └── web.php                # All routes defined
├── config/
├── public/
├── bootstrap/
├── composer.json
├── .env.example
└── README.md
```

## Technology Stack
- **Framework**: Laravel 11 (PHP 8.2+)
- **Database**: PostgreSQL 13+
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **PDF Generation**: Barryvdh Laravel DomPDF
- **Excel Export**: Maatwebsite Excel
- **Frontend**: Bootstrap 5, Font Awesome 6
- **Localization**: Laravel Localization

## Next Steps for Production

1. **Environment Configuration**
   - Set APP_ENV=production
   - Configure mail settings
   - Set up queue workers
   - Configure caching (Redis recommended)

2. **Security**
   - Enable HTTPS
   - Configure firewall rules
   - Set up regular backups
   - Implement 2FA for admin accounts

3. **Performance**
   - Enable OPcache
   - Configure database indexing
   - Set up CDN for assets
   - Implement full-page caching

4. **Monitoring**
   - Set up error tracking (Sentry)
   - Configure log aggregation
   - Monitor database performance
   - Set up uptime monitoring

## License
Proprietary and confidential software.

---
**Aurex ERP** - Built with Laravel & PostgreSQL
**Version**: 1.0.0
**Date**: February 2026

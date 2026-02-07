# Aurex ERP - Quick Start Guide

Get Aurex ERP running in **5 minutes** with Docker!

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/get-started) (Windows/Mac) OR Docker Engine (Linux)
- 4GB RAM available
- 10GB free disk space

---

## Step 1: Download & Extract

Download the `aurex-erp.zip` file and extract it to a folder.

---

## Step 2: Deploy (One Command!)

### Windows

Open **PowerShell as Administrator**:

```powershell
cd C:\path\to\aurex-erp
.\deploy.ps1
```

### macOS / Linux

Open **Terminal**:

```bash
cd /path/to/aurex-erp
chmod +x deploy.sh
./deploy.sh
```

---

## Step 3: Access Your ERP

Wait for the deployment to complete (about 2-3 minutes on first run).

Then open your browser:

**🌐 http://localhost:8080**

### Login Credentials
- **Email**: `admin@aurex.com`
- **Password**: `admin123`

---

## What's Included?

### Demo Data
Your ERP comes pre-loaded with:

| Entity | Count |
|--------|-------|
| Branches | 5 |
| Warehouses | 8 |
| Products | 50+ |
| Customers | 10+ |
| Suppliers | 8+ |
| Quotations | 20+ |
| Sales Orders | 15+ |
| Invoices | 10+ |
| Purchase Orders | 12+ |
| Local Purchases | 8+ |
| Supplier Registrations | 5+ |
| Customer Registrations | 5+ |

### Modules Ready to Use
- ✅ Sales (Quotations, Orders, Invoices)
- ✅ Purchases (Orders, Supply Orders, Local Purchases)
- ✅ Inventory (Items, Warehouses, Stock)
- ✅ Transport (Vehicles, Drivers, Shipments)
- ✅ Maintenance (Work Orders, Schedules)
- ✅ Reports (Sales, Purchases, Suppliers)
- ✅ User Management & Permissions

---

## Useful Commands

| Command | Description |
|---------|-------------|
| `docker-compose logs -f` | View live logs |
| `docker-compose stop` | Stop the ERP |
| `docker-compose start` | Start the ERP |
| `docker-compose restart` | Restart the ERP |
| `docker-compose down` | Remove all containers |
| `./health-check.sh` | Check system health |

---

## Troubleshooting

### Port 8080 is already in use

Edit `docker-compose.yml` and change:
```yaml
ports:
  - "8081:80"  # Change 8080 to 8081
```

Then access at http://localhost:8081

### Docker not running

**Windows**: Start Docker Desktop from Start Menu
**Mac**: Start Docker Desktop from Applications
**Linux**: `sudo systemctl start docker`

### Out of memory

Increase Docker memory limit:
- Docker Desktop → Settings → Resources → Memory → 4GB+

### 404 Error

Wait a bit longer for services to start, then:
```bash
docker-compose restart
```

---

## Production Deployment

For Hostinger, GoDaddy, or other hosting providers, see:

📖 **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)**

---

## Support

Having issues?

1. Check logs: `docker-compose logs`
2. Run health check: `./health-check.sh`
3. See full documentation: [README.md](README.md)

---

**You're all set! Enjoy Aurex ERP! 🎉**

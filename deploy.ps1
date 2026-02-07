# Aurex ERP Deployment Script for Windows
# This script deploys Aurex ERP using Docker Compose

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "  Aurex ERP Deployment Script" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Check if Docker is installed
try {
    $dockerVersion = docker --version
    Write-Host "✓ Docker is installed: $dockerVersion" -ForegroundColor Green
} catch {
    Write-Host "Error: Docker is not installed." -ForegroundColor Red
    Write-Host "Please install Docker Desktop first: https://docs.docker.com/get-docker/"
    exit 1
}

# Check if Docker Compose is installed
try {
    $composeVersion = docker-compose --version
    Write-Host "✓ Docker Compose is installed: $composeVersion" -ForegroundColor Green
} catch {
    Write-Host "Error: Docker Compose is not installed." -ForegroundColor Red
    exit 1
}

Write-Host ""

# Stop existing containers if running
Write-Host "Stopping any existing containers..."
docker-compose down 2>$null

# Build and start containers
Write-Host ""
Write-Host "Building and starting Aurex ERP..." -ForegroundColor Yellow
Write-Host "This may take a few minutes on first run..." -ForegroundColor Yellow
Write-Host ""

docker-compose up --build -d

# Wait for services to be ready
Write-Host ""
Write-Host "Waiting for services to start..."
Start-Sleep -Seconds 15

# Check if containers are running
$containers = docker-compose ps -q
if ($containers) {
    Write-Host ""
    Write-Host "✓ All containers are running" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "✗ Some containers failed to start" -ForegroundColor Red
    Write-Host "Check logs with: docker-compose logs"
    exit 1
}

# Display access information
Write-Host ""
Write-Host "==================================" -ForegroundColor Green
Write-Host "  Aurex ERP Deployed Successfully!" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""
Write-Host "Access your ERP system at:"
Write-Host "  → http://localhost:8080" -ForegroundColor Yellow
Write-Host ""
Write-Host "Default Login Credentials:"
Write-Host "  Email:    admin@aurex.com" -ForegroundColor Yellow
Write-Host "  Password: admin123" -ForegroundColor Yellow
Write-Host ""
Write-Host "Useful Commands:"
Write-Host "  View logs:     docker-compose logs -f"
Write-Host "  Stop ERP:      docker-compose down"
Write-Host "  Restart ERP:   docker-compose restart"
Write-Host "  Update ERP:    docker-compose pull && docker-compose up -d"
Write-Host ""
Write-Host "==================================" -ForegroundColor Cyan

# Open browser
Start-Process "http://localhost:8080"

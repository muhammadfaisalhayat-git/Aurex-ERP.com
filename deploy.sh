#!/bin/bash

# Aurex ERP Deployment Script
# This script deploys Aurex ERP using Docker Compose

set -e

echo "=================================="
echo "  Aurex ERP Deployment Script"
echo "=================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Error: Docker is not installed.${NC}"
    echo "Please install Docker first: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Error: Docker Compose is not installed.${NC}"
    echo "Please install Docker Compose: https://docs.docker.com/compose/install/"
    exit 1
fi

echo -e "${GREEN}✓ Docker and Docker Compose are installed${NC}"
echo ""

# Stop existing containers if running
echo "Stopping any existing containers..."
docker-compose down 2>/dev/null || true

# Build and start containers
echo ""
echo "Building and starting Aurex ERP..."
echo "This may take a few minutes on first run..."
echo ""

docker-compose up --build -d

# Wait for services to be ready
echo ""
echo "Waiting for services to start..."
sleep 10

# Check if containers are running
echo ""
echo "Checking container status..."
if docker-compose ps | grep -q "Up"; then
    echo -e "${GREEN}✓ All containers are running${NC}"
else
    echo -e "${RED}✗ Some containers failed to start${NC}"
    echo "Check logs with: docker-compose logs"
    exit 1
fi

# Display access information
echo ""
echo "=================================="
echo -e "${GREEN}  Aurex ERP Deployed Successfully!${NC}"
echo "=================================="
echo ""
echo "Access your ERP system at:"
echo "  → ${YELLOW}http://localhost:8080${NC}"
echo ""
echo "Default Login Credentials:"
echo "  Email:    ${YELLOW}admin@aurex.com${NC}"
echo "  Password: ${YELLOW}admin123${NC}"
echo ""
echo "Useful Commands:"
echo "  View logs:     docker-compose logs -f"
echo "  Stop ERP:      docker-compose down"
echo "  Restart ERP:   docker-compose restart"
echo "  Update ERP:    docker-compose pull && docker-compose up -d"
echo ""
echo "=================================="

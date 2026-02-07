#!/bin/bash

# Aurex ERP Health Check Script
# Verifies that all services are running correctly

set -e

echo "=================================="
echo "  Aurex ERP Health Check"
echo "=================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

CHECKS_PASSED=0
CHECKS_FAILED=0

check_service() {
    local name=$1
    local container=$2
    
    if docker-compose ps | grep -q "$container.*Up"; then
        echo -e "${GREEN}✓${NC} $name is running"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}✗${NC} $name is not running"
        ((CHECKS_FAILED++))
    fi
}

check_database() {
    echo -n "Checking database connection... "
    
    if docker-compose exec -T db pg_isready -U aurex_user -d aurex_erp > /dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} Database is accessible"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}✗${NC} Database is not accessible"
        ((CHECKS_FAILED++))
    fi
}

check_application() {
    echo -n "Checking application response... "
    
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 2>/dev/null || echo "000")
    
    if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ]; then
        echo -e "${GREEN}✓${NC} Application is responding (HTTP $HTTP_STATUS)"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}✗${NC} Application is not responding (HTTP $HTTP_STATUS)"
        ((CHECKS_FAILED++))
    fi
}

check_tables() {
    echo -n "Checking database tables... "
    
    TABLE_COUNT=$(docker-compose exec -T db psql -U aurex_user -d aurex_erp -t -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public';" 2>/dev/null | tr -d ' ' || echo "0")
    
    if [ "$TABLE_COUNT" -gt "50" ]; then
        echo -e "${GREEN}✓${NC} Database has $TABLE_COUNT tables"
        ((CHECKS_PASSED++))
    else
        echo -e "${YELLOW}!${NC} Database has only $TABLE_COUNT tables (expected 50+)"
        ((CHECKS_PASSED++))
    fi
}

# Check Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}Error: Docker is not running${NC}"
    exit 1
fi

# Run checks
echo "Checking services..."
check_service "PostgreSQL Database" "aurex-db"
check_service "Redis Cache" "aurex-redis"
check_service "Laravel Application" "aurex-app"

echo ""
echo "Checking connectivity..."
check_database
check_application
check_tables

echo ""
echo "=================================="
if [ $CHECKS_FAILED -eq 0 ]; then
    echo -e "${GREEN}All checks passed! ($CHECKS_PASSED/5)${NC}"
    echo ""
    echo "Access your ERP at: http://localhost:8080"
    echo "Login: admin@aurex.com / admin123"
else
    echo -e "${RED}Some checks failed! ($CHECKS_PASSED passed, $CHECKS_FAILED failed)${NC}"
    echo ""
    echo "Troubleshooting:"
    echo "  1. Check logs: docker-compose logs"
    echo "  2. Restart: docker-compose restart"
    echo "  3. Rebuild: docker-compose up --build -d"
fi
echo "=================================="

# ============================================================
# Aurex ERP — Google Cloud Run Deployment Script (PowerShell)
# ============================================================
# Usage: .\deploy-gcp.ps1 -ProjectId "your-gcp-project-id"
# ============================================================

param(
    [Parameter(Mandatory=$true)]
    [string]$ProjectId,

    [string]$Region = "us-central1",
    [string]$ServiceName = "aurex-erp",
    [string]$RepoName = "aurex-erp",
    [string]$DbInstanceName = "aurex-erp-db",
    [string]$DbName = "aurex_erp",
    [string]$DbUser = "aurex_user",
    [string]$DbPassword = "AurexSecure2024!"
)

$ImageBase = "$Region-docker.pkg.dev/$ProjectId/$RepoName/app"
$ImageTag  = "${ImageBase}:latest"

Write-Host "`n===================================================" -ForegroundColor Cyan
Write-Host " Aurex ERP — Google Cloud Run Deployment" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host " Project  : $ProjectId"
Write-Host " Region   : $Region"
Write-Host " Service  : $ServiceName"
Write-Host " Image    : $ImageTag"
Write-Host "===================================================`n"

# --- Step 1: Set project ---
Write-Host "[1/7] Setting GCP project..." -ForegroundColor Yellow
gcloud config set project $ProjectId

# --- Step 2: Enable APIs ---
Write-Host "[2/7] Enabling required GCP APIs..." -ForegroundColor Yellow
gcloud services enable `
    run.googleapis.com `
    sqladmin.googleapis.com `
    artifactregistry.googleapis.com `
    cloudbuild.googleapis.com

# --- Step 3: Create Artifact Registry repo (if not exists) ---
Write-Host "[3/7] Creating Artifact Registry repository..." -ForegroundColor Yellow
gcloud artifacts repositories describe $RepoName --location=$Region 2>$null
if ($LASTEXITCODE -ne 0) {
    gcloud artifacts repositories create $RepoName `
        --repository-format=docker `
        --location=$Region `
        --description="Aurex ERP Docker images"
} else {
    Write-Host "  Repository already exists, skipping."
}

# --- Step 4: Build & Push Docker image ---
Write-Host "[4/7] Building and pushing Docker image..." -ForegroundColor Yellow
gcloud auth configure-docker "$Region-docker.pkg.dev" --quiet
docker build -t $ImageTag .
docker push $ImageTag

# --- Step 5: Create Cloud SQL instance (if not exists) ---
Write-Host "[5/7] Setting up Cloud SQL PostgreSQL..." -ForegroundColor Yellow
$sqlExists = gcloud sql instances describe $DbInstanceName 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "  Creating Cloud SQL instance (this takes ~5 minutes)..."
    gcloud sql instances create $DbInstanceName `
        --database-version=POSTGRES_16 `
        --tier=db-f1-micro `
        --region=$Region `
        --storage-auto-increase `
        --storage-size=10GB

    gcloud sql databases create $DbName --instance=$DbInstanceName
    gcloud sql users create $DbUser --instance=$DbInstanceName --password=$DbPassword
    Write-Host "  Cloud SQL instance created."
} else {
    Write-Host "  Cloud SQL instance already exists, skipping creation."
}

$CloudSqlConnection = "${ProjectId}:${Region}:${DbInstanceName}"

# --- Step 6: Deploy to Cloud Run ---
Write-Host "[6/7] Deploying to Cloud Run..." -ForegroundColor Yellow

# Generate a fresh APP_KEY
$bytes   = New-Object byte[] 32
[Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
$AppKey  = "base64:" + [Convert]::ToBase64String($bytes)

gcloud run deploy $ServiceName `
    --image=$ImageTag `
    --platform=managed `
    --region=$Region `
    --allow-unauthenticated `
    --memory=512Mi `
    --cpu=1 `
    --min-instances=0 `
    --max-instances=3 `
    --timeout=300 `
    --add-cloudsql-instances=$CloudSqlConnection `
    --set-env-vars="APP_NAME=Aurex ERP,APP_ENV=production,APP_DEBUG=false,APP_KEY=$AppKey,DB_CONNECTION=pgsql,DB_HOST=/cloudsql/$CloudSqlConnection,DB_PORT=5432,DB_DATABASE=$DbName,DB_USERNAME=$DbUser,DB_PASSWORD=$DbPassword,CACHE_DRIVER=file,SESSION_DRIVER=file,QUEUE_CONNECTION=sync,DEFAULT_LANGUAGE=en,DEFAULT_CURRENCY=SAR,DEFAULT_TAX_RATE=15"

# --- Step 7: Get service URL ---
Write-Host "[7/7] Getting deployment URL..." -ForegroundColor Yellow
$ServiceUrl = gcloud run services describe $ServiceName --region=$Region --format="value(status.url)"

Write-Host "`n===================================================" -ForegroundColor Green
Write-Host " DEPLOYMENT SUCCESSFUL!" -ForegroundColor Green
Write-Host "===================================================" -ForegroundColor Green
Write-Host " URL      : $ServiceUrl" -ForegroundColor Green
Write-Host " Email    : admin@aurex.com"
Write-Host " Password : admin123"
Write-Host "===================================================`n"

Write-Host "To view logs:" -ForegroundColor Cyan
Write-Host "  gcloud run logs tail $ServiceName --region=$Region`n"

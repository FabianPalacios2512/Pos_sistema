# ============================================================
#  🚀 Deploy 105 POS Pro — PowerShell (Windows nativo)
#  Uso: .\deploy.ps1
# ============================================================

$ErrorActionPreference = "Stop"

$SSH_KEY  = "$env:USERPROFILE\.ssh\id_rsa"
$VPS      = "root@72.61.73.245"
$ROOT     = $PSScriptRoot

Set-Location $ROOT

# ── 1. BUILD FRONTEND ──────────────────────────────────────
Write-Host ""
Write-Host "Compilando frontend..." -ForegroundColor Cyan
npm run build
if ($LASTEXITCODE -ne 0) { Write-Host "ERROR en build" -ForegroundColor Red; exit 1 }

# ── 2. COPIAR dist → backend/public ────────────────────────
Write-Host ""
Write-Host "Copiando dist -> backend/public..." -ForegroundColor Cyan
if (Test-Path "$ROOT\backend\public") {
    Get-ChildItem "$ROOT\dist\*" | Copy-Item -Destination "$ROOT\backend\public\" -Recurse -Force
} else {
    Write-Host "ERROR: backend/public no existe" -ForegroundColor Red; exit 1
}

# ── 3. SUBIR FRONTEND AL VPS ───────────────────────────────
Write-Host ""
Write-Host "Subiendo frontend (solo cambios)..." -ForegroundColor Cyan
& rsync -az --checksum -e "ssh -i `"$SSH_KEY`" -o StrictHostKeyChecking=no" `
    "$ROOT/backend/public/" "${VPS}:/var/www/105pos/backend/public/"
& rsync -az --checksum -e "ssh -i `"$SSH_KEY`" -o StrictHostKeyChecking=no" `
    "$ROOT/dist/" "${VPS}:/var/www/105pos/dist/"

# ── 4. SUBIR BACKEND AL VPS ────────────────────────────────
Write-Host ""
Write-Host "Subiendo backend (solo cambios)..." -ForegroundColor Cyan
& rsync -az --checksum `
    --exclude "node_modules" --exclude "vendor" --exclude "dist" `
    --exclude ".git" --exclude "storage" --exclude ".env" `
    -e "ssh -i `"$SSH_KEY`" -o StrictHostKeyChecking=no" `
    "$ROOT/backend/" "${VPS}:/var/www/105pos/backend/"

# ── 5. LIMPIAR CACHE EN VPS ────────────────────────────────
Write-Host ""
Write-Host "Limpiando cache en VPS..." -ForegroundColor Cyan
& ssh -i "$SSH_KEY" -o StrictHostKeyChecking=no $VPS @"
cd /var/www/105pos/backend && \
php artisan config:clear && php artisan cache:clear && \
php artisan route:clear && php artisan view:clear && \
php artisan config:cache && php artisan route:cache && \
sudo systemctl restart php8.3-fpm && echo 'CACHE OK'
"@

# ── LISTO ──────────────────────────────────────────────────
Write-Host ""
Write-Host "Deploy completado!" -ForegroundColor Green
Write-Host "https://maria.105pos.pro/pos" -ForegroundColor Yellow
Write-Host "Recuerda hacer Ctrl+Shift+R en el navegador para limpiar cache." -ForegroundColor Gray
